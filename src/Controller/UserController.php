<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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

    #[Route('/update/{id}', name:'update_profil', requirements: ['id'=>'\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateProfil(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $isAdmin = $this->isGranted("ROLE_ADMIN");
        if (!$isAdmin && $user !== $this->getUser()) {
            $this->createAccessDeniedException("Réservé aux admins");
        }

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
            'profilForm' => $profilForm,
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name:'delete', requirements: ['id'=>'\d+'], methods: ['POST'])]
    public function deleteProfil(User $user, EntityManagerInterface $em, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        $user=$em->getRepository(User::class)->find($user->getId());
        $em->remove($user);
        $em->flush();

        $isCurrentUser = $this->getUser() === $user;

        if ($isCurrentUser) {
            $tokenStorage->setToken(null);
            $session->invalidate();
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute($isCurrentUser ? 'app_logout' : 'admin_users');
    }
}
