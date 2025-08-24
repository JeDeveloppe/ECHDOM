<?php

namespace App\Form;

use App\Entity\Home;
use App\Entity\FloorLevel;
use App\Entity\HomeEquipment;
use Symfony\Component\Form\AbstractType;
use App\Entity\HomeType as EntityHomeType;
use App\Entity\HomeTypeOfParkingAndGarage;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\HomeTypeOfParkingAndGarageRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description du logement',
                'attr' => [
                    'placeholder' => 'Décrivez votre logement en quelques mots...',
                    // Ajout de la classe Bootstrap
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('surface', IntegerType::class, [
                'label' => 'Surface habitable (en m²)',
                'attr' => [
                    'placeholder' => 'Ex: 50',
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('rooms', IntegerType::class, [
                'label' => 'Nombre de pièces',
                'attr' => [
                    'placeholder' => 'Ex: 3',
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('bedrooms', IntegerType::class, [
                'label' => 'Nombre de chambres',
                'attr' => [
                    'placeholder' => 'Ex: 2',
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('bathrooms', IntegerType::class, [
                'label' => 'Nombre de salles de bain',
                'attr' => [
                    'placeholder' => 'Ex: 1',
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('hasElevator', CheckboxType::class, [
                'label' => 'Ascenseur disponible',
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'required' => false,
            ])
            ->add('hasBalcony', CheckboxType::class, [
                'label' => 'Balcon/terrasse disponible',
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrez une adresse complête',
                ],
            ])
            ->add('latitude', HiddenType::class, [
                'label' => 'Latitude',
            ])
            ->add('longitude', HiddenType::class, [
                'label' => 'Longitude',
            ])
            // ->add('hasGarage', CheckboxType::class, [
            //     'label' => 'Garage disponible',
            //     'attr' => [
            //         'class' => 'form-check-input',
            //     ],
            //     'required' => true,
            // ])
            // ->add('hasParking', CheckboxType::class, [
            //     'label' => 'Parking disponible',
            //     'attr' => [
            //         'class' => 'form-check-input',
            //     ],
            //     'required' => true,
            // ])
            ->add('otherRules', TextareaType::class, [
                'label' => 'Règles supplémentaires',
                'attr' => [
                    'placeholder' => 'Ex: Animaux non acceptés, non-fumeur, ...',
                    'class' => 'form-control',
                ],
                'required' => true,
            ])
            ->add('type', EntityType::class, [
                'class' => EntityHomeType::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un type de logement',
                'label' => 'Type de logement',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('floor', EntityType::class, [
                'class' => FloorLevel::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un étage',
                'label' => 'Étage',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('equipments', EntityType::class, [
                'class' => HomeEquipment::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Équipements',
                'choice_attr' => function() {
                    return ['class' => 'form-check-input'];
                },
                'label_attr' => [
                    'class' => 'form-check-label'
                ]
            ])
            ->add('typeOfGarage', EntityType::class, [
                'class' => HomeTypeOfParkingAndGarage::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un type de garage',
                'label' => 'Garage',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (HomeTypeOfParkingAndGarageRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.isForGarageOnly = :val')
                        ->setParameter('val', true)
                        ->orderBy('p.name', 'ASC');
                },
            ])
            ->add('typeOfParking', EntityType::class, [
                'class' => HomeTypeOfParkingAndGarage::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un type de parking',
                'label' => 'Parking',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'query_builder' => function (HomeTypeOfParkingAndGarageRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.isForParkingOnly = :val')
                        ->setParameter('val', true)
                        ->orderBy('p.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Home::class,
        ]);
    }
}
