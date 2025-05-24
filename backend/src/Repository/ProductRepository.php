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
     * Encuentra los productos más populares basados en ventas totales
     * Este método reemplaza tanto findMostPopularProducts como findMostPopularProductsWithRatio
     * para simplificar la API
     */
    public function findMostPopularProducts(int $limit = 20, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.sales', 'DESC')
            ->setMaxResults($limit);
        
        if ($startDate !== null) {
            $qb->andWhere('p.created_at >= :startDate')
               ->setParameter('startDate', $startDate);
        }
        
        if ($endDate !== null) {
            $qb->andWhere('p.created_at <= :endDate')
               ->setParameter('endDate', $endDate);
        }
        
        return $qb->getQuery()->getResult();
    }

    // Eliminamos el método findMostPopularProductsInDateRange ya que ahora está integrado en findMostPopularProducts
    // Eliminamos el método findMostPopularProductsWithRatio ya que no se utiliza

    /**
     * Cuenta productos creados en un rango de fechas
     */
    public function countByDateRange(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');
        
        if ($startDate !== null) {
            $qb->andWhere('p.created_at >= :startDate')
               ->setParameter('startDate', $startDate);
        }
        
        if ($endDate !== null) {
            $qb->andWhere('p.created_at <= :endDate')
               ->setParameter('endDate', $endDate);
        }
        
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Encuentra productos creados en un rango de fechas
     */
    public function findByDateRange(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null, int $limit = 10, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        
        if ($startDate !== null) {
            $qb->andWhere('p.created_at >= :startDate')
               ->setParameter('startDate', $startDate);
        }
        
        if ($endDate !== null) {
            $qb->andWhere('p.created_at <= :endDate')
               ->setParameter('endDate', $endDate);
        }
        
        return $qb->getQuery()->getResult();
    }


}




