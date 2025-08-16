<?php

namespace App\Service;

use App\Entity\HomeRegulationsAndRestrictions;
use App\Repository\HomeRegulationsAndRestrictionsRepository;
use Doctrine\ORM\EntityManagerInterface;

class HomeRegulationsAndRestrictionsService
{
    private EntityManagerInterface $entityManager;
    private HomeRegulationsAndRestrictionsRepository $homeRegulationsAndRestrictionsRepository;

    public function __construct(EntityManagerInterface $entityManager, HomeRegulationsAndRestrictionsRepository $homeRegulationsAndRestrictionsRepository)
    {
        $this->entityManager = $entityManager;
        $this->homeRegulationsAndRestrictionsRepository = $homeRegulationsAndRestrictionsRepository;
    }

    /**
     * Crée les règles de maison de base si elles n'existent pas déjà.
     */
    public function initializeHomeRegulationsAndRestrictions($io): void
    {
        $homeRegulationsAndRestrictionsList = [
            "Animaux domestiques autorisés",
            "Interdiction de fumer à l'intérieur",
            "Heures de silence de 22h à 7h",
            "Utilisation de la piscine uniquement entre 8h et 20h",
        ];

        $io->note('Initialisation des règles...');
        $io->progressStart(count($homeRegulationsAndRestrictionsList));

        foreach ($homeRegulationsAndRestrictionsList as $regulationData) {
            $existingRegulation = $this->homeRegulationsAndRestrictionsRepository->findOneBy(['name' => $regulationData['name']]);

            if (!$existingRegulation) {
                $newRegulation = new HomeRegulationsAndRestrictions();
                $newRegulation->setName($regulationData['name']);
                $this->entityManager->persist($newRegulation);
            }
            $io->progressAdvance();
        }

        $this->entityManager->flush();
    }
}