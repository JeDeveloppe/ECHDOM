<?php

namespace App\Service;

use App\Entity\FloorLevel;
use App\Entity\UserGender;
use App\Entity\ExchangeStatus;
use App\Repository\FloorLevelRepository;
use App\Repository\UserGenderRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGenderService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserGenderRepository $repository, 
    )
    {
    }

    public function initialize(): void
    {
        // Liste
        $lists = [
            [
                'name' => 'Homme',
                'asset' => 'default_avatar_male.png',
            ],
            [
                'name' => 'Femme',
                'asset' => 'default_avatar_female.png',
            ],
            [
                'name' => 'Je réfère ne pas le dire',
                'asset' => 'default_avatar_nogender.png',
            ],
        ];

        foreach ($lists as $element) {
            // Vérifie si l'équipement existe déjà dans la base de données
            $entity = $this->repository->findOneBy(['name' => $element['name']]);

            // Si l'équipement n'existe pas, on le crée
            if (!$entity) {
                $entity = new UserGender();
                $entity->setName($element['name']);
                $entity->setAssetsLink($element['asset']);

                $this->entityManager->persist($entity);
            }

        }

        // Exécute les requêtes pour sauvegarder les nouveaux équipements
        $this->entityManager->flush();
    }
}
