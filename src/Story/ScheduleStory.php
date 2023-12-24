<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\ScheduleFactory;
use Zenstruck\Foundry\Story;

final class ScheduleStory extends Story
{
    public function build(): void
    {
        ScheduleFactory::createMany(30);
    }
}
