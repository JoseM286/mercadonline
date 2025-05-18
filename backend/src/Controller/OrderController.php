<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/orders')]
#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    // Estados posibles de un pedido
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository
    ) {}

    #[Route('/list', name: 'app_order_list', methods: ['GET'])]
    public function listOrders(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        // Parámetros de paginación
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, $request->query->getInt('limit', 10));
        $offset = ($page - 1) * $limit;

        // Filtro por estado
        $status = $request->query->get('status');
        $criteria = ['user' => $user];
        if ($status) {
            $criteria['status'] = $status;
        }

        // Obtener pedidos
        $orders = $this->orderRepository->findBy(
            $criteria,
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );

        $total = $this->orderRepository->count($criteria);

        // Formatear respuesta
        $ordersData = [];
        foreach ($orders as $order) {
            $ordersData[] = [
                'id' => $order->getId(),
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'shipping_address' => $order->getShippingAddress(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
                'items_count' => $order->getOrderItems()->count()
            ];
        }

        return $this->json([
            'orders' => $ordersData,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    #[Route('/show/{id}', name: 'app_order_show', methods: ['GET'])]
    public function showOrder(#[CurrentUser] User $user, int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return $this->json([
                'error' => 'Pedido no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar que el pedido pertenece al usuario actual
        if ($order->getUser()->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json([
                'error' => 'No tienes permiso para ver este pedido'
            ], Response::HTTP_FORBIDDEN);
        }

        // Obtener items del pedido
        $items = [];
        foreach ($order->getOrderItems() as $item) {
            $items[] = [
                'id' => $item->getId(),
                'product' => [
                    'id' => $item->getProduct()->getId(),
                    'name' => $item->getProduct()->getName()
                ],
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
                'subtotal' => $item->getPrice() * $item->getQuantity()
            ];
        }

        return $this->json([
            'order' => [
                'id' => $order->getId(),
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'shipping_address' => $order->getShippingAddress(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
                'items' => $items
            ]
        ]);
    }

    #[Route('/create', name: 'app_order_create', methods: ['POST'])]
    public function createOrder(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar datos
        if (!isset($data['shipping_address'])) {
            return $this->json([
                'error' => 'La dirección de envío es obligatoria'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar que el carrito no esté vacío
        $cartItems = $this->cartRepository->findBy(['user' => $user]);
        if (count($cartItems) === 0) {
            return $this->json([
                'error' => 'El carrito está vacío'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Iniciar transacción
            $this->entityManager->beginTransaction();

            // Crear pedido
            $order = new Order();
            $order->setUser($user);
            $order->setShippingAddress($data['shipping_address']);
            $order->setStatus(self::STATUS_PENDING);
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($order);

            // Calcular total y crear items del pedido
            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->getProduct();
                $quantity = $cartItem->getQuantity();

                // Verificar stock
                if ($product->getStock() < $quantity) {
                    $this->entityManager->rollback();
                    return $this->json([
                        'error' => "No hay suficiente stock para el producto '{$product->getName()}'"
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Incrementar contador de ventas del producto
                $product->incrementSales($quantity);

                // Crear item del pedido
                $orderItem = new OrderItem();
                $orderItem->setOrderRef($order);
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice($product->getPrice());

                $this->entityManager->persist($orderItem);

                // Actualizar stock
                $product->setStock($product->getStock() - $quantity);

                // Sumar al total
                $subtotal = $product->getPrice() * $quantity;
                $totalAmount += $subtotal;

                // Eliminar item del carrito
                $this->entityManager->remove($cartItem);
            }

            // Actualizar total del pedido
            $order->setTotalAmount((string)$totalAmount);

            // Confirmar transacción
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $this->json([
                'message' => 'Pedido creado correctamente',
                'order' => [
                    'id' => $order->getId(),
                    'total_amount' => $order->getTotalAmount(),
                    'status' => $order->getStatus(),
                    'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->rollback();
            }

            return $this->json([
                'error' => 'Error al crear el pedido: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/cancel', name: 'app_order_cancel', methods: ['PUT'])]
    public function cancelOrder(#[CurrentUser] User $user, int $id): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return $this->json([
                'error' => 'Pedido no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar que el pedido pertenece al usuario actual
        if ($order->getUser()->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json([
                'error' => 'No tienes permiso para cancelar este pedido'
            ], Response::HTTP_FORBIDDEN);
        }

        // Verificar que el pedido puede ser cancelado
        if ($order->getStatus() !== self::STATUS_PENDING && $order->getStatus() !== self::STATUS_PAID) {
            return $this->json([
                'error' => 'Este pedido no puede ser cancelado en su estado actual'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Iniciar transacción
            $this->entityManager->beginTransaction();

            // Actualizar estado del pedido
            $order->setStatus(self::STATUS_CANCELLED);
            $order->setUpdatedAt(new \DateTime());

            // Restaurar stock y decrementar ventas
            foreach ($order->getOrderItems() as $orderItem) {
                $product = $orderItem->getProduct();
                $itemQuantity = $orderItem->getQuantity();
                
                // Restaurar stock
                $product->setStock($product->getStock() + $itemQuantity);
                
                // Decrementar contador de ventas
                // Solo si el pedido estaba en estado PAID, PROCESSING, SHIPPED o DELIVERED
                if (in_array($order->getStatus(), [
                    self::STATUS_PAID, 
                    self::STATUS_PROCESSING, 
                    self::STATUS_SHIPPED, 
                    self::STATUS_DELIVERED
                ])) {
                    // Asegurarse de que no quede negativo
                    $newSales = max(0, $product->getSales() - $itemQuantity);
                    $product->setSales($newSales);
                }
            }

            // Confirmar transacción
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $this->json([
                'message' => 'Pedido cancelado correctamente',
                'order' => [
                    'id' => $order->getId(),
                    'status' => $order->getStatus(),
                    'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->rollback();
            }

            return $this->json([
                'error' => 'Error al cancelar el pedido: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update/{id}/status', name: 'app_order_update_status', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateOrderStatus(int $id, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return $this->json([
                'error' => 'Pedido no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Validar datos
        if (!isset($data['status'])) {
            return $this->json([
                'error' => 'El estado es obligatorio'
            ], Response::HTTP_BAD_REQUEST);
        }

        $status = $data['status'];

        // Verificar que el estado sea válido
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED
        ];

        if (!in_array($status, $validStatuses)) {
            return $this->json([
                'error' => 'Estado no válido',
                'valid_statuses' => $validStatuses
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Si se está cancelando el pedido, restaurar stock y decrementar ventas
            if ($status === self::STATUS_CANCELLED && $order->getStatus() !== self::STATUS_CANCELLED) {
                foreach ($order->getOrderItems() as $orderItem) {
                    $product = $orderItem->getProduct();
                    $itemQuantity = $orderItem->getQuantity();
                    
                    // Restaurar stock
                    $product->setStock($product->getStock() + $itemQuantity);
                    
                    // Decrementar contador de ventas si el pedido estaba en un estado avanzado
                    if (in_array($order->getStatus(), [
                        self::STATUS_PAID, 
                        self::STATUS_PROCESSING, 
                        self::STATUS_SHIPPED, 
                        self::STATUS_DELIVERED
                    ])) {
                        // Asegurarse de que no quede negativo
                        $newSales = max(0, $product->getSales() - $itemQuantity);
                        $product->setSales($newSales);
                    }
                }
            }

            // Actualizar estado del pedido
            $order->setStatus($status);
            $order->setUpdatedAt(new \DateTime());

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Estado del pedido actualizado correctamente',
                'order' => [
                    'id' => $order->getId(),
                    'status' => $order->getStatus(),
                    'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar el estado del pedido: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/admin/list', name: 'app_order_admin_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminListOrders(Request $request): JsonResponse
    {
        // Parámetros de paginación
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, $request->query->getInt('limit', 10));
        $offset = ($page - 1) * $limit;

        // Filtros
        $status = $request->query->get('status');
        $userId = $request->query->get('user_id');

        $criteria = [];
        if ($status) {
            $criteria['status'] = $status;
        }
        if ($userId) {
            $criteria['user'] = $userId;
        }

        // Obtener pedidos
        $orders = $this->orderRepository->findBy(
            $criteria,
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );

        $total = $this->orderRepository->count($criteria);

        // Formatear respuesta
        $ordersData = [];
        foreach ($orders as $order) {
            $ordersData[] = [
                'id' => $order->getId(),
                'user' => [
                    'id' => $order->getUser()->getId(),
                    'email' => $order->getUser()->getEmail(),
                    'name' => $order->getUser()->getName()
                ],
                'total_amount' => $order->getTotalAmount(),
                'status' => $order->getStatus(),
                'shipping_address' => $order->getShippingAddress(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
                'items_count' => $order->getOrderItems()->count()
            ];
        }

        return $this->json([
            'orders' => $ordersData,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
}




