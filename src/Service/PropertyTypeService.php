<?php

namespace App\Service;

use App\Entity\ExchangeStatus;
use App\Entity\HomeEquipment;
use App\Entity\PropertyType;
use App\Entity\NotationCriteria;
use App\Repository\ExchangeStatusRepository;
use App\Repository\PropertyTypeRepository;
use App\Repository\NotationCriteriaRepository;
use Doctrine\ORM\EntityManagerInterface;

class PropertyTypeService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyTypeRepository $PropertyTypeRepository,
    )
    {
    }

    public function initialize(): void
    {
        // Liste des équipements à créer
        $exchangeLists = [
            "Appartement",
            "Maison",
            "Chalet",
            "Villa",
            "Studio",
            "Loft",
            "Bungalow",
            "Cabane",
        ];

        foreach ($exchangeLists as $equipmentData) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $existingEquipment = $this->PropertyTypeRepository->findOneBy(['name' => $equipmentData]);

            // Si l'équipement n'existe pas, on le crée
            if (!$existingEquipment) {
                $newEquipment = new PropertyType();
                $newEquipment->setName($equipmentData);

                $this->entityManager->persist($newEquipment);
            }
        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
