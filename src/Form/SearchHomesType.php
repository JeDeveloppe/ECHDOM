<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class SearchHomesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duration', RangeType::class, [
                'label' => 'Durée du trajet:',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez la durée en minutes',
                    'min' => 10,
                    'max' => 60,
                    'step' => 5,
                    'value' => 10, // Valeur par défaut
                    'class' => 'form-range',
                ],
                'help_attr' => [
                    'class' => 'form-text',
                ],
                'help' => 'Durée maximale choisie: 10 minutes',
            ])
            ->add('distance', RangeType::class, [
                'label' => 'Distance à vol d\'oiseau:',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez la distance en kilomètre à vol d\'oiseau',
                    'min' => 2,
                    'max' => 50,
                    'step' => 1,
                    'value' => 15, // Valeur par défaut
                    'class' => 'form-range',
                ],
                'help_attr' => [
                    'class' => 'form-text',
                ],
                'help' => 'Distance maximale choisie: 15 kilomètres',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
