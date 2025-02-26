<?php

namespace App\EventListener;

use App\Entity\Event;
use App\Enum\EventStatus;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: 'prePersist', entity: Event::class)]
#[AsEntityListener(event: 'preUpdate', entity: Event::class)]
class EventStatusListener
{

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
        $nowMinus1Hour = new \DateTimeImmutable();
        $now = $nowMinus1Hour -> modify('+1 hour');

        if ($event->getStatus() === EventStatus::CANCELLED) {
            return;
        }

        if ($event->getEndsAt() <= $now) {
            $event->setStatus(EventStatus::ENDED);
        } elseif ($event->getBeginsAt() <= $now) {
            $event->setStatus(EventStatus::PENDING);
        } elseif ($event->getRegistrationEndsAt() <= $now) {
            $event->setStatus(EventStatus::CLOSED);
        } else {
            $event->setStatus(EventStatus::OPENED);
        }
    }
}
