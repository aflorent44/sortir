<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/dashboard/users', name: 'dashboard_users', methods: ['GET'])]
    #[isGranted('ROLE_ADMIN')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/dashboard/addresses', name: 'dashboard_addresses', methods: ['GET'])]
    #[isGranted('ROLE_ADMIN')]
    public function addresses(AddressRepository $addressRepository): Response
    {
        $addresses = $addressRepository->findAll();

        return $this->render('admin/addresses.html.twig', [
            'addresses' => $addresses,
        ]);
    }

    #[Route('/dashboard/users/{id}/toggle', name: 'dashboard_user_toggle', methods: ['GET', 'POST'])]
    public function userToggle(User $user, EntityManagerInterface $em, int $id): Response
    {
        $user->setIsActive(!$user->isActive());
        $em->flush();

        $this->addFlash('success', 'Statut du compte mis à jour.');
        return $this->redirectToRoute('admin_dashboard_users');
    }}
