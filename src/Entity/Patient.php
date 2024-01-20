<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ORM\Table(options: ['comment' => 'List of patients'])]
class Patient extends User
{
    public const ICON = 'fa-solid fa-users';
    public const COLOR = 'info';
    #[ORM\Column(length: 64, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez saisir une localité.')]
    private ?string $locality = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez saisir un code postal.')]
    #[Assert\Type(
        type: 'integer',
        message: 'Le code postal ne peux pas contenir de lettre.',
    )]
    private ?string $postal = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $doctor = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'Veuillez saisir une réponse.')]
    private string $origin;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez saisir votre date de naissance.')]
    #[Assert\LessThan(
        value: 'today',
        message: 'La date de naissance ne peux pas être dans le futur.',
    )]
    #[Assert\LessThan(
        value: '-18 years',
        message: 'Vous devez avoir au moins 18 ans pour vous inscrire.',
    )]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Appointment::class, cascade: ['remove'])]
    private Collection $appointments;

    public function __construct()
    {
        parent::__construct();
        $this->appointments = new ArrayCollection();
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(?string $locality): static
    {
        $this->locality = $locality;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): static
    {
        $this->postal = $postal;

        return $this;
    }

    public function getDoctor(): ?string
    {
        return $this->doctor;
    }

    public function setDoctor(?string $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setPatient($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getPatient() === $this) {
                $appointment->setPatient(null);
            }
        }

        return $this;
    }
}
