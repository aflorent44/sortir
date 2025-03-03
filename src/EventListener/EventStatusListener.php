<?php

namespace App\EventListener;

use App\Entity\Event;
use App\Enum\EventStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: 'prePersist', entity: Event::class)]
#[AsEntityListener(event: 'preUpdate', entity: Event::class)]
class EventStatusListener
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(Event $event): void
    {
        $this->updateStatus($event);
    }

    public function preUpdate(Event $event, PreUpdateEventArgs $args): void
    {
        $this->updateStatus($event);
    }

    private function updateStatus(Event $event): void
    {
        $now = new \DateTimeImmutable();

        if ($event->getStatus() === EventStatus::CANCELLED) {
            return;
        }

        if ($event->getEndsAt()->modify('+30 days') <= $now) {
            $event->setStatus(EventStatus::ARCHIVED);
        } elseif ($event->getEndsAt() <= $now) {
            $event->setStatus(EventStatus::ENDED);
        } elseif ($event->getBeginsAt() <= $now) {
            $event->setStatus(EventStatus::PENDING);
        } elseif ($event->getRegistrationEndsAt() <= $now || $event->getParticipants()->count() == $event->getMaxParticipantNumber()) {
            $event->setStatus(EventStatus::CLOSED);
        } else {
            $event->setStatus($event->getStatus());
        }
    }

    public function updateAllEventsStatus(array $events): void
    {
        foreach ($events as $event) {
            $this->updateStatus($event);
        }

        $this->entityManager->flush();
    }

    public function updateOneEventStatus(Event $event): void
    {
        $this->updateStatus($event);
        $this->entityManager->flush();
    }

}
