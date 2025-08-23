<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse e-mail']),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'attr' => ['placeholder' => '06 12 34 56 78'],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Votre numéro de téléphone doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre numéro de téléphone ne peut pas contenir plus de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions d\'utilisation',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}