<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Schedule>
 *
 * @method        Schedule|Proxy                     create(array|callable $attributes = [])
 * @method static Schedule|Proxy                     createOne(array $attributes = [])
 * @method static Schedule|Proxy                     find(object|array|mixed $criteria)
 * @method static Schedule|Proxy                     findOrCreate(array $attributes)
 * @method static Schedule|Proxy                     first(string $sortedField = 'id')
 * @method static Schedule|Proxy                     last(string $sortedField = 'id')
 * @method static Schedule|Proxy                     random(array $attributes = [])
 * @method static Schedule|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ScheduleRepository|RepositoryProxy repository()
 * @method static array<Schedule>|array<Proxy>                 all()
 * @method static array<Schedule>|array<Proxy>                 createMany(int $number, array|callable $attributes = [])
 * @method static array<Schedule>|array<Proxy>                 createSequence(iterable|callable $sequence)
 * @method static array<Schedule>|array<Proxy>                 findBy(array $attributes)
 * @method static array<Schedule>|array<Proxy>                 randomRange(int $min, int $max, array $attributes = [])
 * @method static array<Schedule>|array<Proxy>                 randomSet(int $number, array $attributes = [])
 */
final class ScheduleFactory extends ModelFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'date' => self::faker()->dateTimeBetween('-2 weeks', '+2 months'),
            'isAvailable' => self::faker()->boolean(),
        ];
    }

    protected static function getClass(): string
    {
        return Schedule::class;
    }
}
