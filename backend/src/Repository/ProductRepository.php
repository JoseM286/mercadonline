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
     * Busca productos por nombre (búsqueda parcial)
     */
    public function findByNameLike(string $search, int $limit = 10, int $offset = 0, ?int $categoryId = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('p.name', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
            
        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
            
        return $qb->getQuery()->getResult();
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
}

