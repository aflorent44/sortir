<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setIsActive(true);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        dump($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
//            dd($form->isValid(), $form->getData(), $form->getErrors(true, false));
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //si l'admin a définit un rôle il faut l'ajouter
            if ($this->isGranted('ROLE_ADMIN') && $form->has('roles')) {
                $user->setRoles($form->get('roles')->getData());
            } else {
                $user->setRoles(['ROLE_USER']);
            }
//    dd($user);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email


            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard_users');
            }
            return $security->login($user, 'form_login', 'main');
        }

        dump($user);
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
