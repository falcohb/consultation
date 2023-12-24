<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 *
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Schedule>    findAll()
 * @method array<Schedule>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * @return array<Schedule> Returns an array of Schedule objects
     */
    public function findAllAvailableDate(): array
    {
        // You cannot make a same-day appointment
        $now = new \DateTime();
        $nextDay = (clone $now)->add(new \DateInterval('P1D'));

        return $this->createQueryBuilder('s')
            ->andWhere('s.isAvailable = :isAvailable')
            ->andWhere('s.date > :nextDay')
            ->setParameter('isAvailable', true)
            ->setParameter('nextDay', $nextDay)
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
