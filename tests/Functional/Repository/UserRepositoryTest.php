<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRepositoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function testFindAll(): void
    {
        UserFactory::createMany(2);

        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $this->assertCount(2, $users);
    }
}
