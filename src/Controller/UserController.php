<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user', name: 'user_')]
#[IsGranted('ROLE_USER')]
final class UserController extends AbstractController
{
    private UserRepository $userRepository;

    #[Route('/{id}', name: 'profil', requirements: ['id' => '\d+'])]
    public function getOneUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('user\index.html.twig', [
            'controller_name' => 'Profil',
            'user' => $user,
        ]);
    }

    #[Route('/update/{id}', name: 'update_profil', requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateProfil(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
    {
        $isAdmin = $this->isGranted("ROLE_ADMIN");
        if (!$isAdmin && $user !== $this->getUser()) {
            throw $this->createAccessDeniedException("Réservé aux admins");
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

            if ($newPassword) {
                $encodedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($encodedPassword);
            }

            $profileImage = $profilForm->get('image')->getData();
            if ($profileImage) {
                //gestion de l'image téléchargée
                $originalImageName = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalImageName);
                $newImageName = $safeImageName . '-' . uniqid() . '.' . $profileImage->guessExtension();
                //déplacer le fichier dans le dossier public/uploads
                try {
                    $profileImage->move(
                        $this->getParameter('images_directory'),
                        $newImageName
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('user_update_profil', ['id' => $user->getId()]);
                }
                //maj du User avec le nouveau fichier image
                $user->setProfileImage($newImageName);
            }

            // Mise à jour des autres informations
            $em->persist($user);
            $em->flush();
            dump($user);
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
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deleteProfil(User $user, EntityManagerInterface $em, TokenStorageInterface $tokenStorage,UserRepository $userRepository, SessionInterface $session): Response
    {
        $isAdmin = $this->isGranted("ROLE_ADMIN");
        if (!$isAdmin && $user !== $this->getUser()) {
            $this->createAccessDeniedException("Réservé aux admins");
        }

        $user = $em->getRepository(User::class)->find($user->getId());
        $userRepository->deleteUser($user, $em);
        $em->flush();

        $isCurrentUser = $this->getUser() === $user;
        if ($isCurrentUser) {
            $tokenStorage->setToken(null);
            $session->invalidate();
        }

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute($isCurrentUser ? 'app_logout' : 'admin_dashboard_users');
    }
}
