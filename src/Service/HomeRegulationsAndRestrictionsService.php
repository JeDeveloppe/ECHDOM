<?php

namespace App\Service;

use App\Entity\HomeRegulationsAndRestrictions;
use App\Repository\HomeRegulationsAndRestrictionsRepository;
use Doctrine\ORM\EntityManagerInterface;

class HomeRegulationsAndRestrictionsService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HomeRegulationsAndRestrictionsRepository $homeRegulationsAndRestrictionsRepository
    )
    {
    }

    /**
     * Crée les règles de maison de base si elles n'existent pas déjà.
     */
    public function initialize(): void
    {
        $homeRegulationsAndRestrictionsList = [
            "Animaux domestiques autorisés",
            "Interdiction de fumer à l'intérieur",
            "Heures de silence de 22h à 7h",
            "Utilisation de la piscine uniquement entre 8h et 20h",
        ];

        foreach ($homeRegulationsAndRestrictionsList as $regulationData) {
            $existingRegulation = $this->homeRegulationsAndRestrictionsRepository->findOneBy(['name' => $regulationData]);

            if (!$existingRegulation) {
                $newRegulation = new HomeRegulationsAndRestrictions();
                $newRegulation->setName($regulationData);
                $this->entityManager->persist($newRegulation);
            }

        }

        $this->entityManager->flush();
    }
}