<?php

namespace App\Service;

use App\Entity\ExchangeStatus;
use App\Entity\HomeEquipment;
use App\Repository\ExchangeStatusRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeStatusService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ExchangeStatusRepository $exchange_status_repository
    )
    {
    }

    public function initializeExchangeStatus($io): void
    {
        // Liste des équipements à créer
        $exchangeLists = [
            "Échange accepté",
            "Échange refusé",
            "Échange en cours",
            "Échange terminé",
            "Échange annulé",
            "Échange en attente de confirmation",
        ];

        $io->note('Initialisation des status d\'échange...');
        $io->progressStart(count($exchangeLists));

        foreach ($exchangeLists as $equipmentData) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $existingEquipment = $this->exchange_status_repository->findOneBy(['name' => $equipmentData]);

            // Si l'équipement n'existe pas, on le crée
            if (!$existingEquipment) {
                $newEquipment = new ExchangeStatus();
                $newEquipment->setName($equipmentData);

                $this->entityManager->persist($newEquipment);
            }
            $io->progressAdvance();
        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
