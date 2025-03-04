<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserImportType;
use App\Repository\AddressRepository;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/dashboard/users', name: 'dashboard_users', methods: ['GET', 'POST'])]
    public function users(Request $request, UserRepository $userRepository, EntityManagerInterface $em, CampusRepository $cr, UserPasswordHasherInterface $upi, UserRepository $ur): Response
    {
        $users = $userRepository->findAll();
        $form = $this->createForm(UserImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->importUsers($request, $em, $cr, $upi,$ur);
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'form' => $form->createView()
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
    }

    #[Route('/user/import', name: 'import_users', methods: ['GET', 'POST'])]
    public function importUsers(Request $request, EntityManagerInterface $em, CampusRepository $cr, UserPasswordHasherInterface $upi, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserImportType::class);
        $form->handleRequest($request);
        $users = [];
        dump($users);
        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csv_file')->getData();
            dump($users);
            if ($csvFile) {
                $filePath = $csvFile->getPathname();
                $handle = fopen($filePath, 'r');
                dump($users);
                if ($handle !== false) {
                    fgetcsv($handle); // Ignore la première ligne

                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                        if (count($data) < 6) {
                            continue; // Ignore les lignes mal formées
                        }

                        [$email, $roles, $password, $name, $firstName, $phoneNumber, $campusName, $pseudo, $isActive] = $data;

                        // Vérifier si l'utilisateur existe déjà
                        if ($userRepository->findOneBy(['email' => $email])) {
                            continue;
                        }

                        $user = new User();
                        $user->setEmail($email);
                        $user->setRoles(explode('|', $roles));
                        $user->setPassword($upi->hashPassword($user, $password));
                        $user->setName($name);
                        $user->setFirstName($firstName);
                        $user->setPhoneNumber($phoneNumber);
                        $user->setPseudo($pseudo);
                        $user->setIsActive((bool)$isActive);

                        $campus = $cr->findOneBy(['name' => $campusName]);
                        if ($campus) {
                            $user->setCampus($campus);
                        }
                        dump($user);
                        $users[] = $user;

                        $em->persist($user);
                    }

                    fclose($handle);
                    $em->flush();
                    dump($users);
                    $this->addFlash('success', 'Les utilisateurs ont été importés avec succès !');
                }
            }
        }

        return $this->redirectToRoute('admin_dashboard_users');
    }


}
