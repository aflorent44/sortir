<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function __construct(private Security $security) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom : '
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom : '
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email : '
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo : '
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe s\'il vous plait.',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir minimum {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
//                        new PasswordStrength(),
//                        new NotCompromisedPassword(),
                    ],
                    'label' => 'Mot de passe : ',
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe : ',
                ],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone : '
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'label' => 'Campus : ',
            ]);
        //champs admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            //activation de la protection csrf
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'registration_form',
        ]);
    }
}
