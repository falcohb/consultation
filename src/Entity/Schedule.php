<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use IntlDateFormatter;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\Table(options: ['comment' => 'List of appointment days'])]
class Schedule
{
    public const ICON = 'fas fa-calendar';
    public const COLOR = 'primary';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['comment' => 'Unique identifier of the schedule'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => 'Day of appointment'])]
    private \DateTimeInterface $date;

    #[ORM\Column]
    private bool $isAvailable;

    #[ORM\Column(options: ['comment' => 'Face-to-face or remote'])]
    private bool $isVirtual;

    public function getFormattedDate(): string
    {
        $dateFormatter = IntlDateFormatter::create(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            null,
            IntlDateFormatter::GREGORIAN,
            'EEEE d MMMM Y à HH:mm'
        );

        $formattedDate = $dateFormatter->format($this->date);
        if ($formattedDate !== false) {
            return ucfirst($formattedDate);
        }

        return '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function isIsVirtual(): ?bool
    {
        return $this->isVirtual;
    }

    public function setIsVirtual(bool $isVirtual): static
    {
        $this->isVirtual = $isVirtual;

        return $this;
    }
}
