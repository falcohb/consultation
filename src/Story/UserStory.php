<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public function build(): void
    {
        UserFactory::new()->create([
            'email' => 'omaloteau@gmail.com',
            'firstName' => 'Olivier',
            'lastName' => 'Maloteau',
        ]);

        UserFactory::new()->create([
            'email' => 'cathyassenheim@gmail.com',
            'firstName' => 'Cathy',
            'lastName' => 'Assenheim',
        ]);
    }
}
