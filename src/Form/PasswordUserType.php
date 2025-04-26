<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;


class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actuelPassword', PasswordType::class, [
                'label' => "Votre ancien mot de passe",
                'attr' => [
                    'placeholder' => "Entrer votre ancien mot de passe"
                ],
                'mapped' => false,
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
                    'label' => 'Saisi un nouveau mot de passe', 
                    'attr' => [
                        'placeholder' => 'Entrez votre nouveau mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouveau mot de passe', 
                    'attr' => ['placeholder' => 'Confirmez votre nouveau mot de passe']
                ],
                'mapped' => false,
            ])

            ->add('submit', SubmitType::class, [
                'label' => "Mettre en jour mon password", 
                'attr' => [
                    'class' => "btn btn-success"
                ]
                ])
                ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                    $form = $event->getForm();
                    $user = $form->getConfig()->getOptions()['data'];
                    $passwordHasher = $form->getConfig()->getOption('passwordHasher');
                
                    $isValid = $passwordHasher->isPasswordValid(
                        $user,
                        $form->get('actuelPassword')->getData()
                    );
                
                    if (!$isValid) {
                        $form->get('actuelPassword')->addError(
                            new FormError("Mot de passe actuel incorrect.")
                        );
                    }
                })                
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
