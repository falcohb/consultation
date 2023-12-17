<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $data = $this->getData();
        $entity = $this->createEntityWithData($data);

        $this->assertNull($entity->getId());
        $this->assertSame($entity->getEmail(), $data['email']);
        $this->assertSame($entity->getUserIdentifier(), $data['email']);
        $this->assertSame($entity->getFirstName(), $data['firstName']);
        $this->assertSame($entity->getLastName(), $data['lastName']);
        $this->assertSame($entity->getDisplayName(), $data['displayName']);
        $this->assertSame($entity->getPassword(), $data['password']);
        $this->assertSame($entity->getPlainPassword(), $data['plainPassword']);
        $this->assertSame($entity->getRoles(), $data['roles']);
        $this->assertSame($entity->getIsVerified(), $data['isVerified']);
        $this->assertSame($entity->getDisplayName(), $data['firstName'] . ' ' . $data['lastName']);
        $this->assertSame($entity->getLastLoginAt(), $data['lastLoginAt']);
        $this->assertSame($entity->getCreatedAt(), $data['createdAt']);
        $this->assertSame($entity->getUpdatedAt(), $data['updatedAt']);

        $entity->eraseCredentials();
        $this->assertNull($entity->getPlainPassword());

        $this->assertTrue($entity->hasRole('ROLE_USER'));
        $this->assertFalse($entity->hasRole('ROLE_TEST'));

        $entity->addRole('ROLE_ADMIN');
        $this->assertTrue($entity->hasRole('ROLE_ADMIN'));

        $entity->removeRole('ROLE_ADMIN');
        $this->assertFalse($entity->hasRole('ROLE_ADMIN'));
    }

    private function createEntityWithData(array $data): User
    {
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setPlainPassword($data['plainPassword']);
        $user->setRoles($data['roles']);
        $user->setIsVerified($data['isVerified']);
        $user->setLastLoginAt($data['lastLoginAt']);
        $user->setCreatedAt($data['createdAt']);
        $user->setUpdatedAt($data['updatedAt']);

        return $user;
    }

    private function getData(): array
    {
        return [
            'id' => 1,
            'email' => 'firtsname.lastname@email.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'displayName' => 'John Doe',
            'plainPassword' => 'P@ssw0rd',
            'password' => '$2y$13$RAGn0zAWn0zAIPyRXVuReeURlm63WAldEDxHCtHV2n2.90mwY.z.',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
            'lastLoginAt' => new \DateTime('now'),
            'isVerified' => true,
            'createdAt' => new \DateTime('yesterday'),
            'createdBy' => 1,
            'updatedAt' => new \DateTime('now'),
            'updatedBy' => 1,
            'deletedAt' => new \DateTime('now'),
        ];
    }
}
