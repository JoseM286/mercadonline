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
     * Obtener estadísticas de usuarios respecto a una fecha dada
     */
    public function getStatistics(?\DateTimeInterface $referenceDate = null): array
    {
        // Si no se proporciona fecha de referencia, usar la fecha actual
        $useNextDay = false;
        if ($referenceDate === null) {
            $referenceDate = new \DateTimeImmutable();
        } else {
            // Solo usamos nextDay si se proporciona una fecha específica
            $useNextDay = true;
        }
        
        // Fecha límite para las consultas (la misma referencia o el día siguiente a las 00:00)
        $dateLimit = $referenceDate;
        if ($useNextDay) {
            $dateLimit = (new \DateTime())->setTimestamp($referenceDate->getTimestamp())
                ->modify('+1 day')->setTime(0, 0, 0);
        }

        // Total de usuarios
        $totalUsers = $this->count([]);

        // Total de administradores
        $totalAdmins = $this->count(['role' => User::ROLE_ADMIN]);

        // Usuarios registrados en los últimos 7 días
        $lastWeek = (new \DateTime())->setTimestamp($referenceDate->getTimestamp())->modify('-7 days');
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt >= :lastWeek')
            ->andWhere('u.createdAt <= :dateLimit')
            ->setParameter('lastWeek', $lastWeek)
            ->setParameter('dateLimit', $dateLimit);
        $newUsersLastWeek = $qb->getQuery()->getSingleScalarResult();

        // Usuarios registrados en el último mes
        $lastMonth = (new \DateTime())->setTimestamp($referenceDate->getTimestamp())->modify('-1 month');
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt >= :lastMonth')
            ->andWhere('u.createdAt <= :dateLimit')
            ->setParameter('lastMonth', $lastMonth)
            ->setParameter('dateLimit', $dateLimit);
        $newUsersLastMonth = $qb->getQuery()->getSingleScalarResult();

        // Usuarios registrados en los últimos 6 meses
        $last6Months = (new \DateTime())->setTimestamp($referenceDate->getTimestamp())->modify('-6 months');
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.createdAt >= :last6Months')
            ->andWhere('u.createdAt <= :dateLimit')
            ->setParameter('last6Months', $last6Months)
            ->setParameter('dateLimit', $dateLimit);
        $newUsersLast6Months = $qb->getQuery()->getSingleScalarResult();

        return [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'newUsersLastWeek' => (int) $newUsersLastWeek,
            'newUsersLastMonth' => (int) $newUsersLastMonth,
            'newUsersLast6Months' => (int) $newUsersLast6Months,
            'referenceDate' => $referenceDate->format('Y-m-d H:i:s')
        ];
    }
}





