<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\PatientFactory;
use Zenstruck\Foundry\Story;

final class PatientStory extends Story
{
    public function build(): void
    {
        PatientFactory::new()->create([
            'email' => 'patient@email.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'isVerified' => true,
        ]);

        PatientFactory::createMany(9);
    }
}
