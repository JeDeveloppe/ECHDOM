<?php

namespace App\Service;

use App\Entity\ExchangeStatus;
use App\Entity\HomeEquipment;
use App\Entity\NotationCriteria;
use App\Repository\ExchangeStatusRepository;
use App\Repository\NotationCriteriaRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotationCriteriaService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotationCriteriaRepository $notationCriteriaRepository,
    )
    {
    }

    public function initializeNotationCriterias($io): void
    {
        // Liste des équipements à créer
        $exchangeLists = [
            "Note globale",
            "Propreté",
            "Accessibilité",
        ];

        $io->note('Initialisation des règles...');
        $io->progressStart(count($exchangeLists));

        foreach ($exchangeLists as $equipmentData) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $existingEquipment = $this->notationCriteriaRepository->findOneBy(['name' => $equipmentData]);

            // Si l'équipement n'existe pas, on le crée
            if (!$existingEquipment) {
                $newEquipment = new NotationCriteria();
                $newEquipment->setName($equipmentData);

                $this->entityManager->persist($newEquipment);
            }
            $io->progressAdvance();
        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
