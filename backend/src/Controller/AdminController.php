<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Trait\DateFilterTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    use DateFilterTrait;

    public function __construct(
        private UserRepository $userRepository,
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $usersData = [];
        foreach ($users as $user) {
            $roles = $user->getRoles();
            $createdAt = $user->getCreatedAt();

            $usersData[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => !empty($roles) ? $roles[0] : 'ROLE_USER',
                'createdAt' => $createdAt ? $createdAt->format('Y-m-d H:i:s') : null,
            ];
        }

        return $this->json(['users' => $usersData]);
    }

    #[Route('/users/{id}/role', name: 'app_admin_change_user_role', methods: ['PUT'])]
    public function changeUserRole(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);

            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            $newRole = $data['role'] ?? null;

            if (!$newRole || !in_array($newRole, ['ROLE_USER', 'ROLE_ADMIN'])) {
                return $this->json(['error' => 'Invalid role specified'], Response::HTTP_BAD_REQUEST);
            }

            $user->setRole($newRole);
            $this->entityManager->flush();

            return $this->json(['message' => 'User role updated successfully']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error updating user role: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/users/{id}', name: 'app_admin_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);

            if (!$user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return $this->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error deleting user: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            // Procesar filtros de fecha
            $dateFilters = $this->processDateFilters($request);
            if ($dateFilters['error']) {
                return $this->createDateErrorResponse($dateFilters['error']);
            }

            $startDate = $dateFilters['startDate'];
            $endDate = $dateFilters['endDate'];

            // Límite para productos populares y pedidos recientes
            $limit = min(10, $request->query->getInt('limit', 5));

            // 1. Obtener estadísticas de usuarios
            $userStats = $this->userRepository->getStatistics($startDate, $endDate);

            // 2. Obtener productos populares
            $popularProducts = $this->productRepository->findMostPopularProducts($limit, $startDate, $endDate);
            $popularProductsData = [];

            foreach ($popularProducts as $product) {
                $popularProductsData[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'stock' => $product->getStock(),
                    'sales' => $product->getSales(),
                    'image_path' => $product->getImagePath(),
                    'category' => [
                        'id' => $product->getCategory()->getId(),
                        'name' => $product->getCategory()->getName()
                    ]
                ];
            }

            // 3. Obtener pedidos recientes
            $recentOrders = $this->orderRepository->findByFilters(
                null, // status
                null, // userId
                $startDate,
                $endDate,
                $limit,
                0 // offset
            );

            $recentOrdersData = [];
            foreach ($recentOrders as $order) {
                $recentOrdersData[] = [
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

            // 4. Obtener total de productos
            $totalProducts = $this->productRepository->countByDateRange($startDate, $endDate);

            // 5. Obtener total de ventas y pedidos
            $totalSales = $this->orderRepository->calculateTotalSalesInDateRange($startDate, $endDate);
            $totalOrders = $this->orderRepository->countByFilters(null, null, $startDate, $endDate);

            // Construir respuesta
            return $this->json([
                'users' => [
                    'total' => $userStats['totalUsers'],
                    'totalAdmins' => $userStats['totalAdmins'],
                ],
                'popularProducts' => $popularProductsData,
                'recentOrders' => $recentOrdersData,
                'totalProducts' => $totalProducts,
                'totalSales' => $totalSales,
                'totalOrders' => $totalOrders,
                'dateRange' => [
                    'startDate' => $startDate ? $startDate->format('Y-m-d') : null,
                    'endDate' => $endDate ? $endDate->format('Y-m-d') : null
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al obtener estadísticas del dashboard: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/orders', name: 'app_admin_orders', methods: ['GET'])]
    public function getOrders(Request $request): JsonResponse
    {
        try {
            // Parámetros de paginación
            $page = max(1, $request->query->getInt('page', 1));
            $limit = min(50, $request->query->getInt('limit', 10));
            $offset = ($page - 1) * $limit;

            // Filtros
            $search = $request->query->get('search');
            $status = $request->query->get('status');

            // Procesar filtros de fecha
            $dateFilters = $this->processDateFilters($request);
            if ($dateFilters['error']) {
                return $this->json([
                    'error' => $dateFilters['error']
                ], Response::HTTP_BAD_REQUEST);
            }

            $startDate = $dateFilters['startDate'];
            $endDate = $dateFilters['endDate'];

            // Construir consulta
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('o')
                ->from(Order::class, 'o')
                ->leftJoin('o.user', 'u')
                ->orderBy('o.createdAt', 'DESC');

            // Aplicar filtros
            if ($search) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('o.id', ':search'),
                        $queryBuilder->expr()->like('u.name', ':search'),
                        $queryBuilder->expr()->like('u.email', ':search')
                    )
                )
                    ->setParameter('search', '%' . $search . '%');
            }

            if ($status) {
                $queryBuilder->andWhere('o.status = :status')
                    ->setParameter('status', strtoupper($status));
            }

            if ($startDate) {
                $queryBuilder->andWhere('o.createdAt >= :startDate')
                    ->setParameter('startDate', $startDate);
            }

            if ($endDate) {
                $queryBuilder->andWhere('o.createdAt <= :endDate')
                    ->setParameter('endDate', $endDate);
            }

            // Contar total de resultados para paginación
            $totalQuery = clone $queryBuilder;
            $totalQuery->select('COUNT(o.id)');
            $total = $totalQuery->getQuery()->getSingleScalarResult();

            // Aplicar paginación
            $queryBuilder->setFirstResult($offset)
                ->setMaxResults($limit);

            $orders = $queryBuilder->getQuery()->getResult();

            // Formatear resultados
            $ordersData = [];
            foreach ($orders as $order) {
                $ordersData[] = [
                    'id' => $order->getId(),
                    'user' => $order->getUser() ? [
                        'id' => $order->getUser()->getId(),
                        'name' => $order->getUser()->getName(),
                        'email' => $order->getUser()->getEmail()
                    ] : null,
                    'total_amount' => $order->getTotalAmount(),
                    'status' => $order->getStatus(),
                    'shipping_address' => $order->getShippingAddress(),
                    'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s')
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
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al obtener pedidos: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
