<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cancelReason', TextareaType::class, [
                'label' => 'Motif d\'annulation',
                'attr' => ['rows' => 4,
                    'placeholder' => 'Expliquez brièvement la raison'],
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirmer l\'annulation',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'cancel_event',
        ]);
    }
}
