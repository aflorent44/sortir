<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Enum\EventStatus;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;

class EventRegistrationService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private RouterInterface $router;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer,
                                RouterInterface        $router)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
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

    //annulation event + envoie mail à chaque participant
    public function cancelEvent(Event $event, ?string $cancelReason = null): bool
    {
        //check si l'event est annulable
        $cancelableStatuses = [
            EventStatus::CLOSED,
            EventStatus::OPENED,
            EventStatus::CREATED,
        ];
        if (!in_array($event->getStatus(), $cancelableStatuses)) {
            return false;
        }

        //update statut event
        $event->setStatus(EventStatus::CANCELLED);
        $event->setCancelReason($cancelReason ?? 'Événement annulé');

        //récupère la liste de participant
        $participants = $event->getParticipants();

        //envoie des mails
        foreach ($participants as $participant) {
            $this->sendCancellationEmail($event, $participant);
        }
        $this->entityManager->flush();

        return true;
    }

    private function sendCancellationEmail(Event $event, User $participant): void
    {
        //génère l'url de l'event
        $eventUrl = $this->router->generate('app_event_show', ['id' => $event->getId()], RouterInterface::ABSOLUTE_URL);

        //email
        $email = (new TemplatedEmail())
            ->from('no-reply@sortir.fr')
            ->to($participant->getEmail())
            ->subject('Annulation de votre sortie : ' . $event->getName())
            ->htmlTemplate('emails/event_cancellation.html.twig')
            ->context([
                'participant' => $participant,
                'event' => $event,
                'eventUrl' => $eventUrl,
            ]);

        $this->mailer->send($email);
    }

}
