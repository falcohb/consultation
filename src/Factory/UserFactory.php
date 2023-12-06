<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy                     create(array|callable $attributes = [])
 * @method static User|Proxy                     createOne(array $attributes = [])
 * @method static User|Proxy                     find(object|array|mixed $criteria)
 * @method static User|Proxy                     findOrCreate(array $attributes)
 * @method static User|Proxy                     first(string $sortedField = 'id')
 * @method static User|Proxy                     last(string $sortedField = 'id')
 * @method static User|Proxy                     random(array $attributes = [])
 * @method static User|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static array<User>|array<Proxy>                 all()
 * @method static array<User>|array<Proxy>                 createMany(int $number, array|callable $attributes = [])
 * @method static array<User>|array<Proxy>                 createSequence(iterable|callable $sequence)
 * @method static array<User>|array<Proxy>                 findBy(array $attributes)
 * @method static array<User>|array<Proxy>                 randomRange(int $min, int $max, array $attributes = [])
 * @method static array<User>|array<Proxy>                 randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        $faker = Factory::create('fr_BE');

        return [
            'firstName' => $faker->firstName(),
            'lastName' => $faker->firstName(),
            'email' => self::faker()->unique()->safeEmail(),
            'roles' => ['ROLE_ADMIN'],
            'plainPassword' => 'admin123',
            'isVerified' => true,
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            ->afterInstantiate(function (User $user): void {
                if ($user->getPlainPassword()) {
                    $user->setPassword(
                        $this->passwordHasher->hashPassword($user, $user->getPlainPassword())
                    );
                }
            });
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
