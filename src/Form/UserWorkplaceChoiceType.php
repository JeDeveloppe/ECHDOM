<?php

namespace App\Form;

use App\Entity\Workplace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserWorkplaceChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label' => 'Adresse du lieu de travail',
                'attr' => [
                    'placeholder' => 'Entrez l\'adresse de votre lieu de travail',
                ],
            ])
            ->add('latitude', HiddenType::class, [
                'label' => 'Latitude',
            ])
            ->add('longitude', HiddenType::class, [
                'label' => 'Longitude',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workplace::class,
        ]);
    }
}

