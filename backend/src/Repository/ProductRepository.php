<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Encuentra productos por nombre (búsqueda parcial)
     */
    public function findByNameLike(string $search, int $limit = 10, int $offset = 0, ?int $categoryId = null, array $order = ['name' => 'ASC']): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.name LIKE :search')
            ->setParameter('search', '%' . $search . '%');
            
        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
        
        // Aplicar ordenación
        foreach ($order as $field => $direction) {
            $qb->orderBy('p.' . $field, $direction);
        }
        
        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Cuenta productos por nombre (búsqueda parcial)
     */
    public function countByNameLike(string $search, ?int $categoryId = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.name LIKE :search')
            ->setParameter('search', '%' . $search . '%');
            
        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
            
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Encuentra los productos más vendidos
     */
    public function findMostPopularProducts(int $limit = 20): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.sales', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra los productos más vendidos con ratio de ventas por mes
     */
    public function findMostPopularProductsWithRatio(int $limit = 20): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        // Verificamos si existe la columna created_at
        // Si no existe, usamos una consulta más simple
        try {
            $sql = '
                SELECT p.id, p.name, p.description, p.price, p.stock, p.sales, p.image_path,
                       c.id as category_id, c.name as category_name,
                       CASE 
                         WHEN p.created_at IS NOT NULL THEN p.sales / GREATEST(DATEDIFF(CURRENT_DATE(), p.created_at) / 30, 1)
                         ELSE p.sales
                       END as sales_ratio
                FROM product p
                JOIN category c ON p.category_id = c.id
                WHERE p.sales > 0
                ORDER BY sales_ratio DESC
                LIMIT :limit
            ';
            
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
            $result = $stmt->executeQuery();
            
            $products = $result->fetchAllAssociative();
            
            // Formatear los datos para que coincidan con el formato esperado en el frontend
            foreach ($products as &$product) {
                $product['category'] = [
                    'id' => $product['category_id'],
                    'name' => $product['category_name']
                ];
                unset($product['category_id']);
                unset($product['category_name']);
            }
            
            return $products;
        } catch (\Exception $e) {
            // Si hay un error, fallback a la consulta simple
            return $this->findMostPopularProductsAsArray($limit);
        }
    }

    /**
     * Método de respaldo que devuelve los productos más vendidos en formato de array
     */
    private function findMostPopularProductsAsArray(int $limit = 20): array
    {
        $products = $this->findMostPopularProducts($limit);
        
        $result = [];
        foreach ($products as $product) {
            $result[] = [
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
        
        return $result;
    }

    /**
     * Cuenta productos creados en un rango de fechas
     */
    public function countByDateRange(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');
        
        if ($startDate !== null) {
            $qb->andWhere('p.createdAt >= :startDate')
               ->setParameter('startDate', $startDate);
        }
        
        if ($endDate !== null) {
            $qb->andWhere('p.createdAt <= :endDate')
               ->setParameter('endDate', $endDate);
        }
        
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Encuentra los productos más vendidos en un rango de fechas
     */
    public function findMostPopularProductsInDateRange(int $limit = 20, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.sales', 'DESC')
            ->setMaxResults($limit);
        
        // Si tenemos fechas, filtramos por fecha de creación
        if ($startDate !== null) {
            $qb->andWhere('p.createdAt >= :startDate')
               ->setParameter('startDate', $startDate);
        }
        
        if ($endDate !== null) {
            $qb->andWhere('p.createdAt <= :endDate')
               ->setParameter('endDate', $endDate);
        }
        
        return $qb->getQuery()->getResult();
    }
}







