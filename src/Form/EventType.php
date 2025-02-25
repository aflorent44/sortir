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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('beginsAt', null, [
                'widget' => 'single_text',
            ])
            ->add('endsAt', null, [
                'widget' => 'single_text',
            ])
            ->add('registrationEndsAt', null, [
                'widget' => 'single_text',
            ])
            ->add('description')
            ->add('maxParticipantNumber')
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
