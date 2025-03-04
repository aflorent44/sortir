<?php

namespace App\Command;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-event-reminders',
    description: 'Envoie des rappels aux participants des événements prévus dans 2 jours.',
)]
class SendEventRemindersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Envoi des emails de rappel pour les événements à venir');

        $targetDate = new \DateTimeImmutable('+2 days');
        $targetDateStart = $targetDate->setTime(0, 0, 0);
        $targetDateEnd = $targetDate->setTime(23, 59, 59);

        // Récupérer les événements qui commencent dans 2 jours
        $events = $this->entityManager->createQuery(
            'SELECT e FROM App\Entity\Event e 
             WHERE e.beginsAt BETWEEN :start AND :end'
        )
            ->setParameter('start', $targetDateStart)
            ->setParameter('end', $targetDateEnd)
            ->getResult();

        if (empty($events)) {
            $io->success('Aucun événement prévu dans 2 jours.');
            return Command::SUCCESS;
        }

        $countEmails = 0;

        foreach ($events as $event) {
            foreach ($event->getParticipants() as $participant) {
                $email = (new Email())
                    ->from('noreply@sortir.com')
                    ->to($participant->getEmail())
                    ->subject('Rappel : Votre événement approche !')
                    ->html("
                        <p>Bonjour {$participant->getFirstName()},</p>
                        <p>L'événement auquel vous vous êtes inscrit <strong>{$event->getName()}</strong> commence bientôt !</p>
                        <p>Date : {$event->getBeginsAt()->format('d/m/Y H:i')}</p>
                        <p>Lieu : {$event->getAddress()->getName()}</p>
                        <p>À bientôt !</p>
                    ");

                $this->mailer->send($email);
                $countEmails++;
            }
        }

        $io->success("$countEmails emails de rappel envoyés.");
        return Command::SUCCESS;
    }
}
