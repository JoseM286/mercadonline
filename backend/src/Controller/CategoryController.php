<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository
    ) {}

    #[Route('', name: 'app_category_list', methods: ['GET'])]
    public function listCategories(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();
        
        $categoriesData = [];
        foreach ($categories as $category) {
            $categoriesData[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'productCount' => $category->getProducts()->count()
            ];
        }
        
        return $this->json([
            'categories' => $categoriesData
        ]);
    }
    
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function showCategory(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);
        
        if (!$category) {
            return $this->json([
                'error' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json([
            'category' => [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'productCount' => $category->getProducts()->count()
            ]
        ]);
    }
    
    #[Route('', name: 'app_category_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createCategory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Validar datos
        if (!isset($data['name'])) {
            return $this->json([
                'error' => 'El campo name es obligatorio'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $category = new Category();
            $category->setName($data['name']);
            $category->setDescription($data['description'] ?? null);
            
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            
            return $this->json([
                'message' => 'Categoría creada correctamente',
                'category' => [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                    'productCount' => 0
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al crear la categoría: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/{id}', name: 'app_category_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);
        
        if (!$category) {
            return $this->json([
                'error' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        try {
            if (isset($data['name'])) {
                $category->setName($data['name']);
            }
            
            if (isset($data['description'])) {
                $category->setDescription($data['description']);
            }
            
            $this->entityManager->flush();
            
            return $this->json([
                'message' => 'Categoría actualizada correctamente',
                'category' => [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                    'productCount' => $category->getProducts()->count()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al actualizar la categoría: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCategory(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);
        
        if (!$category) {
            return $this->json([
                'error' => 'Categoría no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Verificar si la categoría tiene productos
        if ($category->getProducts()->count() > 0) {
            return $this->json([
                'error' => 'No se puede eliminar la categoría porque tiene productos asociados'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            
            return $this->json([
                'message' => 'Categoría eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Error al eliminar la categoría: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}