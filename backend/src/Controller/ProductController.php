<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository
    ) {}

    #[Route('/list', name: 'app_product_list', methods: ['GET'])]
    public function listProducts(Request $request): JsonResponse
    {
        // Parámetros de paginación
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, $request->query->getInt('limit', 10));
        $offset = ($page - 1) * $limit;

        // Filtro por categoría
        $categoryId = $request->query->get('category');
        $criteria = [];
        if ($categoryId) {
            $criteria['category'] = $categoryId;
        }

        // Parámetro de ordenación
        $sort = $request->query->get('sort', 'name');
        $order = ['name' => 'ASC']; // Orden predeterminado
        
        if ($sort === 'popularity') {
            $order = ['sales' => 'DESC'];
        }

        // Búsqueda por nombre
        $search = $request->query->get('search');

        // Obtener productos
        if ($search) {
            $products = $this->productRepository->findByNameLike($search, $limit, $offset, $categoryId, $order);
            $total = $this->productRepository->countByNameLike($search, $categoryId);
        } else {
            $products = $this->productRepository->findBy($criteria, $order, $limit, $offset);
            $total = $this->productRepository->count($criteria);
        }

        // Formatear respuesta
        $productsData = [];
        foreach ($products as $product) {
            $productsData[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'image_path' => $product->getImagePath(),
                'category' => [
                    'id' => $product->getCategory()->getId(),
                    'name' => $product->getCategory()->getName()
                ]
            ];
        }

        return $this->json([
            'products' => $productsData,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    #[Route('/show/{id}', name: 'app_product_show', methods: ['GET'])]
    public function showProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json([
                'error' => 'Producto no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'image_path' => $product->getImagePath(),
                'category' => [
                    'id' => $product->getCategory()->getId(),
                    'name' => $product->getCategory()->getName()
                ]
            ]
        ]);
    }

    #[Route('/create', name: 'app_product_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar datos
        if (!isset($data['name']) || !isset($data['price']) || !isset($data['stock']) || !isset($data['category_id'])) {
            return $this->json([
                'error' => 'Los campos name, price, stock y category_id son obligatorios'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar categoría
        $category = $this->categoryRepository->find($data['category_id']);
        if (!$category) {
            return $this->json([
                'error' => 'La categoría no existe'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description'] ?? null);
            $product->setPrice((string)$data['price']);
            $product->setStock((int)$data['stock']);
            $product->setCategory($category);
            
            // Añadir imagen si se proporciona
            if (isset($data['image_path'])) {
                $product->setImagePath($data['image_path']);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Producto creado correctamente',
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'stock' => $product->getStock(),
                    'image_path' => $product->getImagePath(),
                    'category' => [
                        'id' => $product->getCategory()->getId(),
                        'name' => $product->getCategory()->getName()
                    ]
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al crear el producto: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/edit/{id}', name: 'app_product_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json([
                'error' => 'Producto no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        try {
            if (isset($data['name'])) {
                $product->setName($data['name']);
            }

            if (isset($data['description'])) {
                $product->setDescription($data['description']);
            }

            if (isset($data['price'])) {
                $product->setPrice((string)$data['price']);
            }

            if (isset($data['stock'])) {
                $product->setStock((int)$data['stock']);
            }
            
            if (isset($data['image_path'])) {
                $product->setImagePath($data['image_path']);
            }

            if (isset($data['category_id'])) {
                $category = $this->categoryRepository->find($data['category_id']);
                if (!$category) {
                    return $this->json([
                        'error' => 'La categoría no existe'
                    ], Response::HTTP_BAD_REQUEST);
                }
                $product->setCategory($category);
            }

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Producto actualizado correctamente',
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'stock' => $product->getStock(),
                    'image_path' => $product->getImagePath(),
                    'category' => [
                        'id' => $product->getCategory()->getId(),
                        'name' => $product->getCategory()->getName()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar el producto: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/delete/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json([
                'error' => 'Producto no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Producto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/popular', name: 'app_product_popular', methods: ['GET'])]
    public function getPopularProducts(Request $request): JsonResponse
    {
        try {
            // Parámetros
            $limit = min(50, $request->query->getInt('limit', 20));
            $useRatio = $request->query->getBoolean('use_ratio', true);
            
            // Fechas
            $startDate = null;
            $endDate = null;
            
            if ($request->query->has('start_date')) {
                try {
                    $startDate = new \DateTimeImmutable($request->query->get('start_date'));
                } catch (\Exception $e) {
                    return $this->json([
                        'error' => 'Formato de fecha de inicio inválido. Use el formato YYYY-MM-DD.'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            
            if ($request->query->has('end_date')) {
                try {
                    $endDate = new \DateTimeImmutable($request->query->get('end_date'));
                    // Ajustar la fecha de fin para incluir todo el día
                    $endDate = $endDate->modify('+1 day')->modify('-1 second');
                } catch (\Exception $e) {
                    return $this->json([
                        'error' => 'Formato de fecha de fin inválido. Use el formato YYYY-MM-DD.'
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            
            // Obtener productos populares
            if ($useRatio) {
                // Usando ratio de ventas por mes
                $productsData = $this->productRepository->findMostPopularProductsWithRatio($limit);
            } else {
                // Usando solo el total de ventas con filtro de fechas
                $products = $this->productRepository->findMostPopularProductsInDateRange($limit, $startDate, $endDate);
                
                // Formatear respuesta
                $productsData = [];
                foreach ($products as $product) {
                    $productsData[] = [
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
            }
            
            return $this->json([
                'products' => $productsData
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al obtener productos populares: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}



