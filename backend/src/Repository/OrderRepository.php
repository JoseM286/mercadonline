<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Encuentra los pedidos de un usuario con paginación
     */
    public function findByUserPaginated(User $user, int $page = 1, int $limit = 10, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);
            
        if ($status) {
            $qb->andWhere('o.status = :status')
               ->setParameter('status', $status);
        }
            
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Cuenta los pedidos de un usuario
     */
    public function countByUser(User $user, ?string $status = null): int
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.user = :user')
            ->setParameter('user', $user);
            
        if ($status) {
            $qb->andWhere('o.status = :status')
               ->setParameter('status', $status);
        }
            
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    
    /**
     * Encuentra los pedidos recientes
     */
    public function findRecentOrders(int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Encuentra los pedidos por estado
     */
    public function findByStatus(string $status, int $limit = 10, int $offset = 0): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}

