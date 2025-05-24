<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Trait\DateFilterTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        private OrderRepository $orderRepository
    ) {}

    #[Route('/statistics', name: 'app_admin_user_statistics', methods: ['GET'])]
    public function getUserStatistics(Request $request): JsonResponse
    {
        // Procesar filtros de fecha
        $dateFilters = $this->processDateFilters($request);
        if ($dateFilters['error']) {
            return $this->createDateErrorResponse($dateFilters['error']);
        }
        
        $startDate = $dateFilters['startDate'];
        $endDate = $dateFilters['endDate'];
        
        $statistics = $this->userRepository->getStatistics($startDate, $endDate);
        
        return $this->json([
            'statistics' => $statistics
        ]);
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
}



