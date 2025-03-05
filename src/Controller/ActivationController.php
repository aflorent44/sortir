<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ActivationController extends AbstractController
{
    #[Route('/activate/{token}', name: 'app_activate_account')]
    public function activateAccount(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        //trouver le user via le token
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        if (!$user) {
            //si le token est introuvable
            $this->addFlash('error', 'Lien d\'activation invalide ou expiré.');
            return $this->render('activation/error.html.twig');
        }

        //verifier le délai d'expiration du token
        $now = new \DateTimeImmutable();
        $tokenCreatedAt = $user->getActivationTokenCreatedAt();
        dump($tokenCreatedAt);

        //durée de validité du token
        if ($tokenCreatedAt && $tokenCreatedAt->modify('+24 hours') < $now) {
            $this->addFlash('error', 'Le lien d\'activation est expiré. Veuillez contacter un administrateur.');
            return $this->render('activation/error.html.twig');
        }

        dump($tokenCreatedAt);

        //check si le compte est déjà activé
        if ($user->isActive()) {
            $this->addFlash('info', 'Votre compte a déjà été activé.');
            return $this->redirectToRoute('app_login');
        }

        //activation du compte
        $user->setIsActive(true);
        $user->setActivationToken(null); //réinitialise le token
        $user->setActivationTokenCreatedAt(null); //réinitialise la date de création du token

        $entityManager->flush();

        //injection de l'url d'activation au template
        $activationUrl = $urlGenerator->generate('app_activate_account', [
            'token' => $token
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        //page de confirmation
        $this->addFlash('success', 'Votre compte a été activé avec succès !');
        return $this->render('activation/index.html.twig', [
            'activationUrl' => $activationUrl,
        ]);
    }
}
