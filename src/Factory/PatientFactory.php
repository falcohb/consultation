<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Patient;
use App\Repository\PatientRepository;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Patient>
 *
 * @method        Patient|Proxy                     create(array|callable $attributes = [])
 * @method static Patient|Proxy                     createOne(array $attributes = [])
 * @method static Patient|Proxy                     find(object|array|mixed $criteria)
 * @method static Patient|Proxy                     findOrCreate(array $attributes)
 * @method static Patient|Proxy                     first(string $sortedField = 'id')
 * @method static Patient|Proxy                     last(string $sortedField = 'id')
 * @method static Patient|Proxy                     random(array $attributes = [])
 * @method static Patient|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PatientRepository|RepositoryProxy repository()
 * @method static array<Patient>|array<Proxy>                 all()
 * @method static array<Patient>|array<Proxy>                 createMany(int $number, array|callable $attributes = [])
 * @method static array<Patient>|array<Proxy>                 createSequence(iterable|callable $sequence)
 * @method static array<Patient>|array<Proxy>                 findBy(array $attributes)
 * @method static array<Patient>|array<Proxy>                 randomRange(int $min, int $max, array $attributes = [])
 * @method static array<Patient>|array<Proxy>                 randomSet(int $number, array $attributes = [])
 */
final class PatientFactory extends ModelFactory
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
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'roles' => [],
            'plainPassword' => 'user123',
            'email' => $faker->unique()->safeEmail(),
            'origin' => 'Par le bouche à oreille/entourage',
            'locality' => $faker->city(),
            'postal' => $faker->postcode(),
            'phone' => $faker->e164PhoneNumber(),
            'doctor' => $faker->firstName . ' ' . $faker->lastName,
            'birthdate' => self::faker()->dateTimeBetween('-70 years', '-18 years'),
            'isVerified' => self::faker()->boolean(),
            'isActive' => true,
            'lastLoginAt' => self::faker()->dateTimeBetween('-7 days', '0 days'),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (Patient $patient): void {
                if ($patient->getPlainPassword()) {
                    $patient->setPassword(
                        $this->passwordHasher->hashPassword($patient, $patient->getPlainPassword())
                    );
                }
            });
    }

    protected static function getClass(): string
    {
        return Patient::class;
    }
}
