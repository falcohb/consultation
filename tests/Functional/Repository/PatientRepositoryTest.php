<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\Patient;
use App\Factory\PatientFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PatientRepositoryTest extends KernelTestCase
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
        PatientFactory::createMany(5);

        $patients = $this->entityManager
            ->getRepository(Patient::class)
            ->findAll();

        $this->assertCount(5, $patients);
    }
}
