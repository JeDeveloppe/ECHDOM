<?php

namespace App\Service;

use App\Entity\PropertyTypeOfParkingAndGarage;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PropertyTypeOfParkingAndGarageRepository;

class PropertyTypeOfParkingAndGarageService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyTypeOfParkingAndGarageRepository $repository,
    )
    {
    }

    public function initialize(): void
    {
        $lists = [
            ['name' => 'Aucun', 'isForParkingOnly' => true, 'isForGarageOnly' => true],
            ['name' => 'Privé', 'isForParkingOnly' => true, 'isForGarageOnly' => true],
            ['name' => 'Collectif', 'isForParkingOnly' => true, 'isForGarageOnly' => false],
            ['name' => 'Sous-sol', 'isForParkingOnly' => false, 'isForGarageOnly' => true],
            ['name' => 'Dans la rue', 'isForParkingOnly' => true, 'isForGarageOnly' => false],
        ];

        foreach ($lists as $element) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $entity = $this->repository->findOneBy(['name' => $element['name']]);

            // Si l'équipement n'existe pas, on le crée
            if (!$entity) {
                $entity = new PropertyTypeOfParkingAndGarage();
            }
            
            $entity->setName($element['name']);
            $entity->setIsForParkingOnly($element['isForParkingOnly']);
            $entity->setIsForGarageOnly($element['isForGarageOnly']);

            $this->entityManager->persist($entity);

        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
