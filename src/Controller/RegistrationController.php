<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    private UserRegistrationService $userRegistrationService;
    public function __construct(
        UserRegistrationService $userRegistrationService
    ) {
        $this->userRegistrationService = $userRegistrationService;
    }

    #[Route('/registration', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager): Response
    {
        $user = new User();
//        $user->setIsActive(true);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode/hash du mdp (plainPassword)
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //si l'admin a définit un rôle il faut l'ajouter
            if ($this->isGranted('ROLE_ADMIN') && $form->has('roles')) {
                $user->setRoles($form->get('roles')->getData());
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            //appel du service pour l'inscription
            try {
                $this->userRegistrationService->registerUser($user);
                $this->addFlash('success', "Un email de confirmation va vous être envoyé.");

                if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard_users');
                }
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', "Erreur lors de l'inscription : " . $e->getMessage());
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

}
