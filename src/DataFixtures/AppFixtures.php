<?php

namespace App\DataFixtures;

use App\Entity\Home;
use App\Entity\User;
use App\Entity\Workplace;
use App\Repository\FloorLevelRepository;
use App\Repository\HomeEquipmentRepository;
use App\Repository\HomeTypeRepository;
use App\Service\ExchangeStatusService;
use App\Service\FloorLevelService;
use App\Service\GeocodingService;
use App\Service\HomeEquipmentService;
use App\Service\HomeRegulationsAndRestrictionsService;
use App\Service\HomeTypeService;
use App\Service\NotationCriteriaService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Faker\Factory as FakerFactory; // <-- Ajout de cette ligne pour l'alias

class AppFixtures extends Fixture
{
    public function __construct(
        private GeocodingService $geocodingService,
        private FloorLevelService $floorLevelService,
        private FloorLevelRepository $floorLevelRepository, // <-- Ajout du repository FloorLevelRepository
        private HomeTypeService $homeTypeService, // <-- Ajout du service HomeTypeService
        private HomeTypeRepository $homeTypeRepository, // <-- Ajout du repository HomeTypeRepository
        private ExchangeStatusService $exchangeStatusService, // <-- Ajout du service ExchangeStatusService
        private HomeRegulationsAndRestrictionsService $homeRegulationsAndRestrictionsService, // <-- Ajout du service HomeRegulationsAndRestrictionsService
        private NotationCriteriaService $notationCriteriaService, // <-- Ajout du service Notation
        private HomeEquipmentService $homeEquipmentService, // <-- Ajout du service HomeEquipmentService
        private HomeEquipmentRepository $homeEquipmentRepository, // <-- Ajout du repository HomeEquipmentRepository
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
    
        //?quelques services sont nécessaires pour initialiser les données
        //?on initialise les niveaux de sol
        $this->floorLevelService->initialize();
        $this->homeTypeService->initialize();
        $this->exchangeStatusService->initialize();
        $this->homeRegulationsAndRestrictionsService->initialize();
        $this->notationCriteriaService->initialize();
        $this->homeEquipmentService->initialize();


        //?on charge la librairie faker pour générer des données aléatoires
        // Correction de la ligne suivante
        $faker = FakerFactory::create('fr_FR');

        //?on crée 50 utilisateurs avec leur lieu de travail, le lieu de résidence
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setPhoneNumber($faker->phoneNumber);
            $user->setPassword($faker->password);
            $user->setBiography($faker->text(200));
            $user->setVerified(true);
            $user->setPointsGained(0);
            $user->setPointsSpent(0);

            //?on géocode l'adresse du lieu de travail
            $workplaceAddress = $this->geocodingService->getRandomAddress("fr");
            $workplaceCoordinates = $this->geocodingService->geocodeAddress($workplaceAddress['address']);

            //?on crée le lieu de travail
            $workplace = new Workplace();
            $workplace->setAddress($workplaceCoordinates['address']);
            $workplace->setLatitude($workplaceCoordinates['y']);
            $workplace->setLongitude($workplaceCoordinates['x']);
            $workplace->setOwner($user);
            //?on ajoute le lieu de travail à l'utilisateur
            $user->addWorkplace($workplace);

            //?on géocode l'adresse de la résidence
            $homeAddress = $this->geocodingService->getRandomAddress("fr");
            $homeCoordinates = $this->geocodingService->geocodeAddress($homeAddress['address']);

            //?on récupère un niveau de sol aléatoire
            $floorLevels = $this->floorLevelRepository->findAll();
            $randomFloorLevel = array_rand($floorLevels);

            //?on récupere un type de maison aléatoire
            $homeTypes = $this->homeTypeRepository->findAll();
            $randomHomeType = array_rand($homeTypes);

            //?on crée le lieu de résidence
            $home = new Home();
            $home->setAddress($homeCoordinates['address']);
            $home->setLatitude($homeCoordinates['y']);
            $home->setLongitude($homeCoordinates['x']);
            $home->setOwner($user); 
            $home->setDescription($faker->text(200));
            $home->setSurface($faker->numberBetween(20, 200));
            $home->setRooms($faker->numberBetween(1, 10));
            $home->setBedrooms($faker->numberBetween(1, 5));
            $home->setBathrooms($faker->numberBetween(1, 5));
            $home->setFloor($floorLevels[$randomFloorLevel]);
            $home->setType($homeTypes[$randomHomeType]);
            $home->setHasElevator($faker->boolean);
            $home->setHasBalcony($faker->boolean);
            $home->setOtherRules($faker->text(200));
            //?on renseigne les équipements de la maison
            $equipments = $this->homeEquipmentRepository->findAll();
            foreach ($equipments as $equipment) {
                if ($faker->boolean(50)) { // 50% de chance d'ajouter l'équipement
                    $home->addEquipment($equipment);
                }
            }
            //?on ajoute le lieu de résidence à l'utilisateur
            $user->addHome($home);

            //?on persiste l'utilisateur et son lieu de travail
            $manager->persist($home);
            $manager->persist($user);
            $manager->persist($workplace);
        }

        //?on flush les données en base de données
        $manager->flush();

    }

    
}