<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Appointment;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentVoter extends Voter
{
    public const VIEW = 'VIEW_APPOINTMENT';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW])
            && $subject instanceof Appointment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Appointment) {
            throw new \Exception('Wrong type somehow passed');
        }

        if (!$this->security->isGranted('ROLE_USER')) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->checkIfUserIsCreatorAppointment($user, $subject);
        }
        return false;
    }

    protected function checkIfUserIsCreatorAppointment(User $user, Appointment $subject): bool
    {
        $userEmail = $user->getEmail();
        $appointmentOwner = $subject->getPatient();

        if ($appointmentOwner instanceof UserInterface) {
            if ($userEmail === $appointmentOwner->getUserIdentifier()) {
                return true;
            }
        }

        return false;
    }
}
