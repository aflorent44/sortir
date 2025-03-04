<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserRegistrationService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface        $mailer,
        UrlGeneratorInterface  $urlGenerator
    )
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function registerUser(User $user): void
    {
        //générer le token d'activation unique
        $activationToken = bin2hex(random_bytes(32));
        $user->setActivationToken($activationToken);
        $user->setIsActive(false); //inactif

        //save de l'utilisateur en bdd
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        //envoie de l'email de confirmation
        $this->sendActivationEmail($user);
    }

    private function sendActivationEmail(User $user): void
    {
        //génère l'url d'activation
        $activationUrl = $this->urlGenerator->generate('app_activate_account', [
            'token' => $user->getActivationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        //crée et envoie l'email
        $email = (new Email())
            ->from('no-reply@sortir.fr')
            ->to($user->getEmail())
            ->subject('Activez de votre compte')
            ->html(sprintf( '<h1>Activation de votre compte</h1>
                    <p>Cliquez sur le lien ci-dessous pour activer votre compte :</p>
                    <a href="%s">Activer mon compte</a>
                    <p>Si vous n\'avez pas créé de compte, ignorez cet email.</p>',
                $activationUrl
            ));

        $this->mailer->send($email);
    }
}
