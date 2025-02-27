<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;

class ProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'required' => false,
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmation du nouveau mot de passe',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $profilForm = $context->getRoot();
                        $newPassword = $profilForm->get('newPassword')->getData();
                        if ($newPassword !== $value) {
                            $context->buildViolation('Les mots de passe ne correspondent pas')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Ajouter un photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/jpg'],
                        'mimeTypesMessage' => 'Votre image doit être au format .jpeg, .jpg, .png ou .bmp'
                    ])
                ]
            ])
        ->add('save', SubmitType::class, [
            'label' => 'Enregistrer',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
