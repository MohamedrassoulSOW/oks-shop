<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Indiquer votre prénom ...'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Indiquer votre nom ...'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre addresse',
                'attr' => [
                    'placeholder' => 'Indiquer votre addresse ...'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Votre addres postal',
                'attr' => [
                    'placeholder' => 'Indiquer votre addresse postal ...'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre région',
                'attr' => [
                    'placeholder' => 'Indiquer votre région ...'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Votre pays'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre Téléphone', 
                'attr' => [
                    'placeholder' => 'Indiquer votre numéro téléphone ...'
                    ]
                ])

                ->add('submit', SubmitType::class, [
                    'label' => 'Sauvegarder', 
                    'attr' => [
                        'class' => 'btn btn-success'
                        ]
                    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
