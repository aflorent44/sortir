<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => false,
                'label' => 'Campus',
                'mapped' => false,
                'required' => false,
                'placeholder' => 'Tous les campus',
            ])
            ->add('name', null, [
                'label' => 'Le nom de la sortie contient...',
                'mapped' => false,
                'required' => false,
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Entre',
                'mapped' => false,
                'required' => false,
                'widget' => 'single_text', // Utiliser un champ de type "date" en un seul champ
            ])
            ->add('dateMax', DateType::class, [
                'label' => 'et',
                'mapped' => false,
                'required' => false,
                'widget' => 'single_text', // Utiliser un champ de type "date" en un seul champ
            ])
            ->add('isHost', checkboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur.ice',
                'mapped' => false,
                'required' => false,
            ])
            ->add('isParticipant', checkboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit.e',
                'mapped' => false,
                'required' => false,
            ])
            ->add('isNotParticipant', checkboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit.e',
                'mapped' => false,
                'required' => false,
            ])
            ->add('ended', checkboxType::class, [
                'label' => 'Sorties terminées',
                'mapped' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ])
            ->add('reset', ResetType::class, [
                'label' => 'Réinitialiser',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
