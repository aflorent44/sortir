<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Enum\EventStatus;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class EventRegistrationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function canUserRegister(Event $event, User $user): bool
    {
        return $event->getParticipants()->count() < $event->getMaxParticipantNumber()
            && $event->getHost() !== $user
            && !$event->getParticipants()->contains($user)
            && $event->getStatus() === EventStatus::OPENED;
    }

    public function canAdminOrHostUnregisterUser(Event $event, User $currentUser, User $participantToRemove): bool
    {
        return ($event->getHost() === $currentUser)
            && $event->getParticipants()->contains($participantToRemove)
            && ($event->getStatus() == EventStatus::OPENED || $event->getStatus() == EventStatus::CREATED);
    }

    public function canUserUnregister(Event $event, User $user): bool
    {
        return $event->getParticipants()->contains($user)
            && ($event->getStatus() == EventStatus::OPENED || $event->getStatus() == EventStatus::CLOSED);
    }

    public function registerUser(Event $event, User $user): bool
    {
        if (!$this->canUserRegister($event, $user)) {
            return false;
        }

        $event->getParticipants()->add($user);
        $this->entityManager->flush();

        return true;
    }

    public function unregisterUser(Event $event, User $user): bool
    {
        if (!$this->canUserUnregister($event, $user)) {
            return false;
        }

        $event->removeParticipant($user);
        $this->entityManager->flush();

        return true;
    }

    public function unregisterParticipantByAdmin(Event $event, User $currentUser, User $participantToRemove): bool
    {
        if (!$this->canAdminOrHostUnregisterUser($event, $currentUser, $participantToRemove)) {
            return false;
        }

        $event->removeParticipant($participantToRemove);
        $this->entityManager->flush();

        return true;
    }
}