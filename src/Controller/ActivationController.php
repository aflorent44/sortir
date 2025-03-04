<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActivationController extends AbstractController
{
    #[Route('/activate/{token}', name: 'app_activate_account')]
    public function activateAccount(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        //trouver le user via le token
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        if (!$user) {
            //si le token est introuvable
            $this->addFlash('error', 'Lien d\'activation invalide ou expiré.');
            return $this->render('activation/error.html.twig');
        }

        //activation du compte
        $user->setIsActive(true);
        $user->setActivationToken(null); //réinitialise le token

        $entityManager->flush();

        //page de confirmation
        $this->addFlash('success', 'Votre compte a été activé avec succès !');
        return $this->render('activation/index.html.twig', [
//            'controller_name' => 'ActivationController',
        ]);
    }
}
