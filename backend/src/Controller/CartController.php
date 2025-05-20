<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository
    ) {}

    #[Route('/list', name: 'app_cart_list', methods: ['GET'])]
    public function listCartItems(#[CurrentUser] User $user): JsonResponse
    {
        $cartItems = $this->cartRepository->findBy(['user' => $user]);

        $items = [];
        $total = 0;

        foreach ($cartItems as $item) {
            $product = $item->getProduct();
            $subtotal = $product->getPrice() * $item->getQuantity();
            $total += $subtotal;

            $items[] = [
                'id' => $item->getId(),
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'image_path' => $product->getImagePath()
                ],
                'quantity' => $item->getQuantity(),
                'subtotal' => $subtotal
            ];
        }

        return $this->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    #[Route('/add', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar datos
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            return $this->json([
                'error' => 'Los campos product_id y quantity son obligatorios'
            ], Response::HTTP_BAD_REQUEST);
        }

        $productId = $data['product_id'];
        $quantity = max(1, (int)$data['quantity']); // Asegurar que la cantidad sea al menos 1

        // Verificar producto
        $product = $this->productRepository->find($productId);
        if (!$product) {
            return $this->json([
                'error' => 'Producto no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar stock
        if ($product->getStock() < $quantity) {
            return $this->json([
                'error' => 'No hay suficiente stock disponible'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Verificar si el producto ya está en el carrito
            $cartItem = $this->cartRepository->findOneBy([
                'user' => $user,
                'product' => $product
            ]);

            if ($cartItem) {
                // Actualizar cantidad
                $newQuantity = $cartItem->getQuantity() + $quantity;

                // Verificar stock nuevamente con la cantidad actualizada
                if ($product->getStock() < $newQuantity) {
                    return $this->json([
                        'error' => 'No hay suficiente stock disponible'
                    ], Response::HTTP_BAD_REQUEST);
                }

                $cartItem->setQuantity($newQuantity);
            } else {
                // Crear nuevo item en el carrito
                $cartItem = new Cart();
                $cartItem->setUser($user);
                $cartItem->setProduct($product);
                $cartItem->setQuantity($quantity);
                $cartItem->setCreatedAt(new \DateTimeImmutable());

                $this->entityManager->persist($cartItem);
            }

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Producto añadido al carrito correctamente',
                'cart_item' => [
                    'id' => $cartItem->getId(),
                    'product' => [
                        'id' => $product->getId(),
                        'name' => $product->getName(),
                        'price' => $product->getPrice(),
                        'image_path' => $product->getImagePath()
                    ],
                    'quantity' => $cartItem->getQuantity(),
                    'subtotal' => $product->getPrice() * $cartItem->getQuantity()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al añadir el producto al carrito: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['PUT'])]
    public function updateCartItem(#[CurrentUser] User $user, int $id, Request $request): JsonResponse
    {
        $cartItem = $this->cartRepository->find($id);

        if (!$cartItem) {
            return $this->json([
                'error' => 'Item del carrito no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar que el item pertenece al usuario actual
        if ($cartItem->getUser()->getId() !== $user->getId()) {
            return $this->json([
                'error' => 'No tienes permiso para modificar este item'
            ], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        // Validar datos
        if (!isset($data['quantity'])) {
            return $this->json([
                'error' => 'El campo quantity es obligatorio'
            ], Response::HTTP_BAD_REQUEST);
        }

        $quantity = max(1, (int)$data['quantity']); // Asegurar que la cantidad sea al menos 1
        $product = $cartItem->getProduct();

        // Verificar stock
        if ($product->getStock() < $quantity) {
            return $this->json([
                'error' => 'No hay suficiente stock disponible'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $cartItem->setQuantity($quantity);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Cantidad actualizada correctamente',
                'cart_item' => [
                    'id' => $cartItem->getId(),
                    'product' => [
                        'id' => $product->getId(),
                        'name' => $product->getName(),
                        'price' => $product->getPrice(),
                        'image_path' => $product->getImagePath()
                    ],
                    'quantity' => $cartItem->getQuantity(),
                    'subtotal' => $product->getPrice() * $cartItem->getQuantity()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar la cantidad: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['DELETE'])]
    public function removeFromCart(#[CurrentUser] User $user, int $id): JsonResponse
    {
        $cartItem = $this->cartRepository->find($id);

        if (!$cartItem) {
            return $this->json([
                'error' => 'Item del carrito no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar que el item pertenece al usuario actual
        if ($cartItem->getUser()->getId() !== $user->getId()) {
            return $this->json([
                'error' => 'No tienes permiso para eliminar este item'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->entityManager->remove($cartItem);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Producto eliminado del carrito correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al eliminar el producto del carrito: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['DELETE'])]
    public function clearCart(#[CurrentUser] User $user): JsonResponse
    {
        try {
            $cartItems = $this->cartRepository->findBy(['user' => $user]);

            foreach ($cartItems as $item) {
                $this->entityManager->remove($item);
            }

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Carrito vaciado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al vaciar el carrito: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/count', name: 'app_cart_count', methods: ['GET'])]
    public function getCartCount(#[CurrentUser] User $user): JsonResponse
    {
        try {
            $count = $this->cartRepository->count(['user' => $user]);

            return $this->json([
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al obtener el conteo del carrito: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
