<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\User;
use App\Enum\EventStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom de la sortie',
            ])
            ->add('beginsAt', null, [
                'widget' => 'single_text',
                'label' => 'Date et heure de début de la sortie'
            ])
            ->add('endsAt', null, [
                'widget' => 'single_text',
                'label' => 'Date et heure de fin de la sortie'
            ])
            ->add('registrationEndsAt', null, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description et infos',
            ])
            ->add('maxParticipantNumber', null, [
                'label' => 'Nombre de places'
            ])
            ->add('campuses', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Campus',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
