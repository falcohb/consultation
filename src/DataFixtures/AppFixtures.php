<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\PatientStory;
use App\Story\ScheduleStory;
use App\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserStory::load();
        PatientStory::load();
        ScheduleStory::load();
    }
}
