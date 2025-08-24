<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Entity\User;
use App\Entity\Workplace;
use App\Repository\FloorLevelRepository;
use App\Repository\PropertyEquipmentRepository;
use App\Repository\PropertyTypeOfParkingAndGarageRepository;
use App\Repository\PropertyTypeRepository;
use App\Service\ExchangeStatusService;
use App\Service\FloorLevelService;
use App\Service\GeocodingService;
use App\Service\PropertyTypeOfParkingAndGarageService;
use App\Service\PropertyTypeService;
use App\Service\NotationCriteriaService;
use App\Service\PropertyEquipmentService;
use App\Service\PropertyRegulationsAndRestrictionsService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Faker\Factory as FakerFactory; // <-- Ajout de cette ligne pour l'alias
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private GeocodingService $geocodingService,
        private UserPasswordHasherInterface $userPasswordHasher,
        private FloorLevelService $floorLevelService,
        private FloorLevelRepository $floorLevelRepository, // <-- Ajout du repository FloorLevelRepository
        private PropertyTypeService $PropertyTypeService, // <-- Ajout du service PropertyTypeService
        private PropertyTypeRepository $PropertyTypeRepository, // <-- Ajout du repository PropertyTypeRepository
        private ExchangeStatusService $exchangeStatusService, // <-- Ajout du service ExchangeStatusService
        private PropertyRegulationsAndRestrictionsService $propertyRegulationsAndRestrictionsService, // <-- Ajout du service HomeRegulationsAndRestrictionsService
        private NotationCriteriaService $notationCriteriaService, // <-- Ajout du service Notation
        private PropertyEquipmentService $propertyEquipmentService, // <-- Ajout du service HomeEquipmentService
        private PropertyEquipmentRepository $propertyEquipmentRepository, // <-- Ajout du repository HomeEquipmentRepository
        private PropertyTypeOfParkingAndGarageService $PropertyTypeOfParkingAndGarageService, // <-- Ajout du service PropertyTypeOfParkingAndGarageService
        private PropertyTypeOfParkingAndGarageRepository $PropertyTypeOfParkingAndGarageRepository // <-- Ajout du repository PropertyTypeOfParkingAndGarageRepository
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
    
        //?quelques services sont nécessaires pour initialiser les données
        //?on initialise les niveaux de sol
        $this->floorLevelService->initialize();
        $this->PropertyTypeService->initialize();
        $this->exchangeStatusService->initialize();
        $this->propertyRegulationsAndRestrictionsService->initialize();
        $this->notationCriteriaService->initialize();
        $this->propertyEquipmentService->initialize();
        $this->PropertyTypeOfParkingAndGarageService->initialize();


        //?on charge la librairie faker pour générer des données aléatoires
        // Correction de la ligne suivante
        $faker = FakerFactory::create('fr_FR');
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));

        //?on crée 50 utilisateurs avec leur lieu de travail, le lieu de résidence
        for ($i = 0; $i < 20; $i++) {
            $user = new User();

            //?si c'est le premier utilisateur, on le définit comme admin
            if($i === 0) {
                $user->setEmail($_ENV['ADMIN_EMAIL']);
                $user->setRoles(['ROLE_SUPER_ADMIN']);
                $user->setFirstName('Admin');
                $user->setLastName('User');
                $user->setPhoneNumber($_ENV['ADMIN_PHONE']);
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $_ENV['ADMIN_PASSWORD']));

            }else{

                $user->setEmail($faker->email);
                $user->setFirstName($faker->firstName);
                $user->setLastName($faker->lastName);
                $user->setPhoneNumber($faker->phoneNumber);
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $faker->password));
            }

            //?les autres champs de l'utilisateur en commun
            $user->setCreatedAt($now);
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
            $propertyAddress = $this->geocodingService->getRandomAddress("fr");
            $propertyCoordinates = $this->geocodingService->geocodeAddress($propertyAddress['address']);

            //?on récupère un niveau de sol aléatoire
            $floorLevels = $this->floorLevelRepository->findAll();
            $randomFloorLevel = array_rand($floorLevels);

            //?on récupere un type de maison aléatoire
            $PropertyTypes = $this->PropertyTypeRepository->findAll();
            $randomPropertyType = array_rand($PropertyTypes);

            //?on crée le lieu de résidence
            $property = new Property();
            $property->setAddress($propertyCoordinates['address']);
            $property->setLatitude($propertyCoordinates['y']);
            $property->setLongitude($propertyCoordinates['x']);
            $property->setOwner($user); 
            $property->setDescription($faker->text(200));
            $property->setSurface($faker->numberBetween(20, 200));
            $property->setRooms($faker->numberBetween(1, 10));
            $property->setBedrooms($faker->numberBetween(1, 5));
            $property->setBathrooms($faker->numberBetween(1, 5));
            $property->setFloor($floorLevels[$randomFloorLevel]);
            $property->setType($PropertyTypes[$randomPropertyType]);
            $property->setHasElevator($faker->boolean);
            $property->setHasBalcony($faker->boolean);
            $property->setOtherRules($faker->text(200));
            $property->setHasGarage($faker->boolean(30)); // 30% de chances d'avoir un garage
            $property->setHasParking($faker->boolean(50)); // 50% de chances d'avoir un parking
            //?on renseigne les équipements de la maison
            $equipments = $this->propertyEquipmentRepository->findAll();
            foreach ($equipments as $equipment) {
                if ($faker->boolean(50)) { // 50% de chance d'ajouter l'équipement
                    $property->addEquipment($equipment);
                }
            }
            //?on renseigne les types de parking et garage de la maison
            $typeOfParkingAndGarages = $this->PropertyTypeOfParkingAndGarageRepository->findAll();
            if($property->hasGarage()) {
                foreach ($typeOfParkingAndGarages as $typeOfParkingAndGarage) {
                    if ($typeOfParkingAndGarage->isForGarageOnly() && $faker->boolean(50)) { // 50% de chance d'ajouter le type de garage
                        $property->setTypeOfGarage($typeOfParkingAndGarage);
                    }
                }
            }
            if($property->hasParking()) {
                foreach ($typeOfParkingAndGarages as $typeOfParkingAndGarage) {
                    if ($typeOfParkingAndGarage->isForParkingOnly() && $faker->boolean(50)) { // 50% de chance d'ajouter le type de parking
                        $property->setTypeOfParking($typeOfParkingAndGarage);
                    }
                }
            }

            //?on ajoute le lieu de résidence à l'utilisateur
            $user->addProperty($property);

            //?on persiste l'utilisateur et son lieu de travail
            $manager->persist($property);
            $manager->persist($user);
            $manager->persist($workplace);
        }

        //?on flush les données en base de données
        $manager->flush();

    }

    
}