<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Appointment>    findAll()
 * @method array<Appointment>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function getUpcomingAppointmentByPatient(int $patientId): ?Appointment
    {
        $date = new \DateTime();

        try {
            return $this->createQueryBuilder('a')
                ->andWhere('a.patient = :id')
                ->setParameter('id', $patientId)
                ->andWhere('s.date >= :date')
                ->setParameter('date', $date)
                ->join('a.schedule', 's')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return array<Appointment> Returns an array of Schedule objects
     */
    public function getPastAppointmentByPatient(int $patientId): array
    {
        $date = new \DateTime();

        return $this->createQueryBuilder('a')
            ->andWhere('a.patient = :id')
            ->setParameter('id', $patientId)
            ->andWhere('s.date < :date')
            ->setParameter('date', $date)
            ->join('a.schedule', 's')
            ->getQuery()
            ->getResult()
        ;
    }
}
