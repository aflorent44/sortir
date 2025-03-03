<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['id' => 'name', 'placeholder' => "Choisiss un nom pour l'adresse..."],
                'label' =>"C'est où ?",
            ])
            ->add('city', TextType::class, [
                'attr' => ['id' => 'city', 'placeholder' => 'Entre une ville ou un code postal...'],
                'label' =>'Ville',
            ])
            ->add('zipCode', TextType::class, [
                'attr' => ['id' => 'zipCode', 'placeholder' => 'Entre un code postal...'],
                'label' =>'Code Postal',
            ])
            ->add('street', TextType::class, [
                'attr' => ['id' => 'street', 'placeholder' => 'Entre une adresse...'],
                'label' =>'Adresse',
            ])
            ->add('lat', TextType::class, [
                'attr' => ['id' => 'lat'],
                'label' =>'latitude',
            ])
            ->add('lng', TextType::class, [
                'attr' => ['id' => 'lng'],
                'label' =>'longitude',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
