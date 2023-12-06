<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method array<User> findAll()
 * @method array<User> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function countUsersAdminByRole(): int
    {
        try {
            return (int) $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%ROLE_ADMIN%')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException) {
            return 0;
        }
    }

    public function countUsersByRole(): int
    {
        try {
            return (int) $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '[]')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * @return array<int, string>
     */
    public function suggestionsAutocompleteEmail(): array
    {
        $suggestions = [];
        /** @var array<User> $users */
        $users = $this->findAll();
        foreach ($users as $user) {
            if ($user->getEmail() !== null) {
                $suggestions[] = $user->getEmail();
            }
        }

        return $suggestions;
    }

    /**
     * Update last login datetime
     *
     * @throws \Exception
     * @throws \Doctrine\DBAL\Exception
     * @codeCoverageIgnore
     */
    final public function setLastLogin(User $user): void
    {
        // Don't use ORM to avoid wrong updated date
        $sql = 'UPDATE user set last_login_at = :lastLoginAt where id = :id';
        //set parameters
        $params = [];
        $params['lastLoginAt'] = (new \DateTime())->format('Y-m-d H:i:s');
        $params['id'] = $user->getId();
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->executeStatement($params);
    }
}
