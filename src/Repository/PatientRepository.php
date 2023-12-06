<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Patient;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Patient>
 *
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<Patient>    findAll()
 * @method array<Patient>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    final public function setLastLogin(User $user): void
    {
        // Don't use ORM to avoid wrong updated date
        $sql = 'UPDATE patient set last_login_at = :lastLoginAt where id = :id';
        //set parameters
        $params = [];
        $params['lastLoginAt'] = (new \DateTime())->format('Y-m-d H:i:s');
        $params['id'] = $user->getId();
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->executeStatement($params);
    }
}
