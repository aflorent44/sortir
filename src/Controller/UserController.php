<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'user_')]
#[IsGranted('ROLE_USER')]
final class UserController extends AbstractController
{
    #[Route('/{id}', name: 'profil', requirements: ['id' => '\d+'])]
    public function getOneUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('user\index.html.twig', [
            'controller_name' => 'Mon Profil',
            'user' => $user,
        ]);
    }

    #[Route('/update/{id}', name: 'update_profil', requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateProfil(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        // Sauvegarde du campus
        $campus = $user->getCampus();

        $profilForm = $this->createForm(ProfilFormType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            // Récupération des champs du formulaire
            $oldPassword = $profilForm->get('oldPassword')->getData();
            $newPassword = $profilForm->get('newPassword')->getData();
            $confirmPassword = $profilForm->get('confirmPassword')->getData();

            // Vérification de l'ancien mot de passe
            if ($oldPassword && !$userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', 'Ancien mot de passe incorrect.');
                return $this->redirectToRoute('user_update_profil', ['id' => $user->getId()]);
            }

            // Vérification de la confirmation du nouveau mot de passe
//            if ($newPassword && $newPassword !== $confirmPassword) {
//                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
//                return $this->redirectToRoute('user_update_profil', ['id' => $user->getId()]);
//            }

            // Hash du nouveau mot de passe
            if ($newPassword) {
                $encodedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($encodedPassword);
            }

            // Mise à jour des autres informations
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profil modifié avec succès');

            // Redirection nécessaire pour Turbo Drive
            return $this->redirectToRoute('user_profil', ['id' => $user->getId()]);
        }

        // Affichage du formulaire
        return $this->render('user/update.html.twig', [
            'controller_name' => 'Modifier mes infos : ',
            'profilForm' => $profilForm->createView(),
        ]);
    }
}
