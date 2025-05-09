<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Votre prenom",
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 20
                        ])
                ],
                'attr' => [
                    'placeholder' => "Entrer votre prenom"
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Votre nom",
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 20
                        ])
                ], 
                'attr' => [
                    'placeholder' => "Entrer votre nom"
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre adresse mail", 
                'attr' => [
                    'placeholder' => "Entrer votre email"
                ]
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 20
                        ])
                ],
                'first_options' => [
                    'label' => 'Votre mot de passe', 
                    'attr' => [
                        'placeholder' => 'Entrez votre mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe', 
                    'attr' => ['placeholder' => 'Confirmez votre mot de passe']
                ],
                'mapped' => false,
            ])

            ->add('submit', SubmitType::class, [
                'label' => "Valider", 
                'attr' => [
                    'class' => "btn btn-success"
                ]
            ])
       ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                ])
            ],
            'data_class' => User::class,
        ]);
    }
}
