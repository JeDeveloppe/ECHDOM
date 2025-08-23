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
        //?on ajoute l'option pour le temps de trajet entre la maison et le lieu de travail
        $timeBetweenmMyHomeAndMyWorkplace = $options['timeBetweenmMyHomeAndMyWorkplace'];

        $builder
            ->add('duration', RangeType::class, [
                'label' => 'Durée du trajet:',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez la durée en minutes',
                    'min' => 10,
                    // 'max' => round($timeBetweenmMyHomeAndMyWorkplace / 2),//TODO: on pourrait mettre une valeur par défaut
                    'max' => $timeBetweenmMyHomeAndMyWorkplace ,
                    'step' => 1,
                    'value' => 10, // Valeur par défaut
                    'class' => 'form-range',
                ],
                'help_attr' => [
                    'class' => 'form-text',
                ],
                'help' => 'Durée maximale choisie: 10 minutes',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'timeBetweenmMyHomeAndMyWorkplace' => null, // Option pour le
        ]);
    }
}
