<?php

namespace App\Service;

use App\Entity\HomeEquipment;
use App\Repository\HomeEquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class HomeEquipmentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HomeEquipmentRepository $homeEquipmentRepository
    )
    {
    }

    /**
     * Crée les équipements de maison de base s'ils n'existent pas déjà.
     * Cette méthode est utile pour peupler la base de données au démarrage.
     *
     * @param array $equipmentList La liste des équipements à créer. Chaque élément
     * doit être un tableau avec les clés 'name' et 'logo'.
     */
    public function initializeHomeEquipments($io): void
    {
        // Liste des équipements à créer
        $equipmentList = [
            ['name' => 'Wi-Fi', 'logo' => 'fas fa-wifi'],
            ['name' => 'Parking', 'logo' => 'fas fa-parking'],
            ['name' => 'Piscine', 'logo' => 'fas fa-swimming-pool'],
            ['name' => 'Jardin', 'logo' => 'fas fa-utensils'],
            ['name' => 'Lave-vaisselle', 'logo' => 'fas fa-dishwasher'],
            ['name' => 'Climatisation', 'logo' => 'fas fa-snowflake'],
            ['name' => 'Lave-linge', 'logo' => 'fas fa-tshirt'],
            ['name' => 'Cuisine équipée', 'logo' => 'fas fa-utensils'],
            ['name' => 'Cheminée', 'logo' => 'fas fa-fire'],
            ['name' => 'Système de sécurité', 'logo' => 'fas fa-shield-alt'],
            ['name' => 'Barbecue', 'logo' => 'fas fa-fire'],
            // Ajoutez d'autres équipements ici
        ];

        $io->note('Initialisation des équipements de maison...');
        $io->progressStart(count($equipmentList));

        foreach ($equipmentList as $equipmentData) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $existingEquipment = $this->homeEquipmentRepository->findOneBy(['name' => $equipmentData['name']]);

            // Si l'équipement n'existe pas, on le crée
            if (!$existingEquipment) {
                $newEquipment = new HomeEquipment();
                $newEquipment->setName($equipmentData['name']);
                $newEquipment->setLogo($equipmentData['logo']);

                $this->entityManager->persist($newEquipment);
            }
            $io->progressAdvance();
        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
