<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Appointment;
use App\Entity\Schedule;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

class AppointmentSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Appointment) {
            return;
        }

        $entityManager = $args->getObjectManager();
        $schedule = $entity->getSchedule();

        if (!$schedule instanceof Schedule) {
            return;
        }

        $schedule->setIsAvailable(false);

        $entityManager->persist($schedule);
        $entityManager->flush();
    }
}
