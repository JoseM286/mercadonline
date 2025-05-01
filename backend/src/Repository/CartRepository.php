<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * Obtiene el total del carrito para un usuario
     */
    public function getCartTotal(User $user): float
    {
        $qb = $this->createQueryBuilder('c')
            ->select('SUM(p.price * c.quantity) as total')
            ->join('c.product', 'p')
            ->where('c.user = :user')
            ->setParameter('user', $user);
            
        $result = $qb->getQuery()->getSingleScalarResult();
        
        return $result ? (float)$result : 0;
    }
    
    /**
     * Obtiene el número de items en el carrito
     */
    public function getCartItemCount(User $user): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user')
            ->setParameter('user', $user);
            
        return (int)$qb->getQuery()->getSingleScalarResult();
    }
    
    /**
     * Obtiene el número total de productos en el carrito (suma de cantidades)
     */
    public function getCartProductCount(User $user): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('SUM(c.quantity)')
            ->where('c.user = :user')
            ->setParameter('user', $user);
            
        $result = $qb->getQuery()->getSingleScalarResult();
        
        return $result ? (int)$result : 0;
    }
}

