<?php

namespace App\Command;

use App\Repository\LegalInformationRepository;
use App\Service\ExchangeStatusService;
use App\Service\FloorLevelService;
use App\Service\LegalInformationService;
use App\Service\PropertyTypeOfParkingAndGarageService;
use App\Service\PropertyTypeService;
use App\Service\NotationCriteriaService;
use App\Service\PropertyEquipmentService;
use App\Service\PropertyRegulationsAndRestrictionsService;
use App\Service\UserGenderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-app',
    description: 'Crée les équipements de maison initiaux dans la base de données.'
)]
class InitApp extends Command
{
    public function __construct(
        private PropertyEquipmentService $propertyEquipmentService,
        private PropertyRegulationsAndRestrictionsService $propertyRegulationsAndRestrictionsService,
        private ExchangeStatusService $exchangeStatusService,
        private NotationCriteriaService $notationCriteriaService,
        private PropertyTypeService $PropertyTypeService,
        private FloorLevelService $floorLevelService, // <-- Ajout du service FloorLevelService
        private PropertyTypeOfParkingAndGarageService $PropertyTypeOfParkingAndGarageService, // <-- Ajout du service PropertyTypeOfParkingAndGarageService
        private UserGenderService $userGenderService, // <-- Ajout du service UserGenderService
        private LegalInformationService $legalInformationService, // <-- Ajout du service LegalInformationService
        private LegalInformationRepository $legalInformationRepository // <-- Ajout du repository LegalInformationRepository
    )
    {
         parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Appelle le service pour initialiser la base de données
        $io->title('Ajout des genres utilisateur');
        $this->userGenderService->initialize();
        $io->success('Ajout des genres utilisateur fait avec succès.');

        $io->title('Création / mise à jour des informations légales');
        $this->legalInformationService->creationLegalInformation($io);
        $io->success('Création / mise à jour des informations légales fait avec succès.');

        $io->section('Initialisation des équipements de maison');
        $this->propertyEquipmentService->initialize();
        $io->success('Initialisation des équipements de maison terminée avec succès.');

        $io->section('Initialisation des règles et restrictions de maison');
        $this->propertyRegulationsAndRestrictionsService->initialize();
        $io->success('Initialisation des règles et restrictions de maison terminée avec succès.');

        $io->section('Initialisation des status d\'échange');
        $this->exchangeStatusService->initialize();
        $io->success('Initialisation des status d\'échange terminée avec succès.');

        $io->section('Initialisation des critères de notation');
        $this->notationCriteriaService->initialize();
        $io->success('Initialisation des critères de notation terminée avec succès.');

        $io->section('Initialisation des types de maison');
        $this->PropertyTypeService->initialize();
        $io->success('Initialisation des types de maison terminée avec succès.');

        $io->section('Initialisation des niveaux de sol');
        $this->floorLevelService->initialize(); // <-- Appel de la méthode pour initialiser les niveaux de sol
        $io->success('Initialisation des niveaux de sol terminée avec succès.');

        $io->section('Initialisation des types de parking et garage');
        $this->PropertyTypeOfParkingAndGarageService->initialize(); // <-- Appel de la méthode pour initialiser les types de parking et garage
        $io->success('Initialisation des types de parking et garage terminée avec succès.');

        $io->success('Iniatialisation fait avec succès.');

        return Command::SUCCESS;
    }
}