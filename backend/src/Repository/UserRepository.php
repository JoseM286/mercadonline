<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Obtener estadísticas de usuarios respecto a un rango de fechas
     */
    public function getStatistics(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): array
    {
        // Si no se proporciona fecha de fin, usar la fecha actual
        if ($endDate === null) {
            $endDate = new \DateTimeImmutable();
        }
        
        // Consulta base para filtrar por fecha
        $qb = $this->createQueryBuilder('u');
        
        // Aplicar filtro de fecha de inicio si se proporciona
        if ($startDate !== null) {
            $qb->andWhere('u.createdAt >= :startDate')
               ->setParameter('startDate', $startDate);
        }
        
        // Aplicar filtro de fecha de fin
        $qb->andWhere('u.createdAt <= :endDate')
           ->setParameter('endDate', $endDate);
        
        // Total de usuarios en el rango de fechas
        $totalUsers = $qb->select('COUNT(u.id)')
                         ->getQuery()
                         ->getSingleScalarResult();
        
        // Total de administradores en el rango de fechas
        $qbAdmins = $this->createQueryBuilder('u')
                         ->select('COUNT(u.id)')
                         ->where('u.role = :role')
                         ->setParameter('role', User::ROLE_ADMIN);
        
        if ($startDate !== null) {
            $qbAdmins->andWhere('u.createdAt >= :startDate')
                     ->setParameter('startDate', $startDate);
        }
        
        $qbAdmins->andWhere('u.createdAt <= :endDate')
                 ->setParameter('endDate', $endDate);
        
        $totalAdmins = $qbAdmins->getQuery()->getSingleScalarResult();
        
        return [
            'totalUsers' => (int) $totalUsers,
            'totalAdmins' => (int) $totalAdmins,
            'startDate' => $startDate ? $startDate->format('Y-m-d') : null,
            'endDate' => $endDate->format('Y-m-d'),
        ];
    }
}








