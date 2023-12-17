<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Patient;
use PHPUnit\Framework\TestCase;

class PatientTest extends TestCase
{
    public function testSetterGetter(): void
    {
        $data = $this->getData();
        $entity = $this->createEntityWithData($data);

        $this->assertNull($entity->getId());
        $this->assertSame($entity->getPostal(), $data['postal']);
        $this->assertSame($entity->getLocality(), $data['locality']);
        $this->assertSame($entity->getDoctor(), $data['doctor']);
        $this->assertSame($entity->getPhone(), $data['phone']);
        $this->assertSame($entity->getOrigin(), $data['origin']);
        $this->assertSame($entity->getBirthdate(), $data['birthdate']);
        $this->assertSame($entity->getIsActive(), $data['isActive']);
    }

    private function createEntityWithData(array $data): Patient
    {
        $patient = new Patient();
        $patient->setLocality($data['locality']);
        $patient->setPostal($data['postal']);
        $patient->setDoctor($data['doctor']);
        $patient->setPhone($data['phone']);
        $patient->setOrigin($data['origin']);
        $patient->setBirthdate($data['birthdate']);
        $patient->setIsActive($data['isActive']);

        return $patient;
    }

    private function getData(): array
    {
        return [
            'locality' => 'Brussels',
            'postal' => '1380',
            'doctor' => 'Dr Good',
            'phone' => '0475222333',
            'origin' => 'web',
            'birthdate' => new \DateTime(),
            'isActive' => true,
        ];
    }
}
