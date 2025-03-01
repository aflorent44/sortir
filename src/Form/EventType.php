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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['placeholder' => "On fait quoi ?"],
                'label' => 'Nomme ta sortie :',
            ])
            ->add('beginsAt', null, [
                'widget' => 'single_text',
                'label' => "Quand-est-ce qu'on sort?"
            ])
            ->add('endsAt', null, [
                'widget' => 'single_text',
                'label' => 'Et on rentre quand ?'
            ])
            ->add('registrationEndsAt', null, [
                'widget' => 'single_text',
                'label' => "Date limite d'inscription"
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['placeholder' => "Dis-nous en plus !"],
                'label' => 'Description et infos',
            ])
            ->add('maxParticipantNumber', null, [
                'attr' => ['placeholder' => "1000 !"],
                'label' => "Y'a combien de places? "
            ])
            ->add('campuses', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Campus',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('publish', SubmitType::class, [
                'label' => 'Publier',
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
