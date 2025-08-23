<?php

namespace App\Form;

use App\Entity\HomeAvailability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class HomeAvailabilityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date de début (à midi)',
            ])
            ->add('endAt', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Récurrence jusqu\'au (à midi)',
            ])
            ->add('weeklyDays', ChoiceType::class, [
                'choices' => [
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                    'Dimanche' => 7,
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => 'Jours de la semaine',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HomeAvailability::class,
        ]);
    }
}
