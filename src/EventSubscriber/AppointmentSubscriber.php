<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Appointment;
use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
class AppointmentSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    /**
     * Set isAvailable to false when an appointment is created.
     */
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
