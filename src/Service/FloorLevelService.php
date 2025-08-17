<?php

namespace App\Service;

use App\Entity\ExchangeStatus;
use App\Entity\FloorLevel;
use App\Repository\FloorLevelRepository;
use Doctrine\ORM\EntityManagerInterface;

class FloorLevelService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FloorLevelRepository $repository, 
    )
    {
    }

    public function initialize(): void
    {
        // Liste
        $lists = [
            'Rez-de-chaussée',
            '1er étage',
            '2ème étage',
            '3ème étage',
            '4ème étage',
            '5ème étage',
            '6ème étage',
            '7ème étage',
            '8ème étage',
            '9ème étage',
            '10ème étage',
        ];

        foreach ($lists as $element) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $entity = $this->repository->findOneBy(['name' => $element]);

            // Si l'équipement n'existe pas, on le crée
            if (!$entity) {
                $entity = new FloorLevel();
                $entity->setName($element);

                $this->entityManager->persist($entity);
            }

        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
