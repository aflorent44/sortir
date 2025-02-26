<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'user_')]
#[IsGranted('ROLE_USER')]
final class UserController extends AbstractController
{
    #[Route('/{id}', name:'profil', requirements: ['id'=>'\d+'])]
    public function update(int $id, UserRepository $userRepository): Response
    {
        $profil = $userRepository->find($id);

        return $this->render('user\index.html.twig', [
            'controller_name' => 'Mon Profil',
            'user' => $profil,
        ]);
    }

    #[Route('/update/{id}', name:'update', requirements: ['id'=>'\d+'])]
    public function updateProfil(User $user, Request $request, EntityManagerInterface $em): Response
    {

        $profilForm = $this->createForm(RegistrationFormType::class, $user);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil modifié avec succès');
            return $this->redirectToRoute('user_index', ['id' => $user->getId()]);
        }
        return $this->render('user\update.html.twig', [
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
