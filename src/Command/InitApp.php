<?php

namespace App\Command;

use App\Service\ExchangeStatusService;
use App\Service\FloorLevelService;
use App\Service\HomeEquipmentService;
use App\Service\HomeRegulationsAndRestrictionsService;
use App\Service\HomeTypeOfParkingAndGarageService;
use App\Service\HomeTypeService;
use App\Service\NotationCriteriaService;
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
        private HomeEquipmentService $homeEquipmentService,
        private HomeRegulationsAndRestrictionsService $homeRegulationsAndRestrictionsService,
        private ExchangeStatusService $exchangeStatusService,
        private NotationCriteriaService $notationCriteriaService,
        private HomeTypeService $homeTypeService,
        private FloorLevelService $floorLevelService, // <-- Ajout du service FloorLevelService
        private HomeTypeOfParkingAndGarageService $homeTypeOfParkingAndGarageService // <-- Ajout du service HomeTypeOfParkingAndGarageService
    )
    {
         parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Appelle le service pour initialiser la base de données
        $io->section('Initialisation des équipements de maison');
        $this->homeEquipmentService->initialize();
        $io->success('Initialisation des équipements de maison terminée avec succès.');

        $io->section('Initialisation des règles et restrictions de maison');
        $this->homeRegulationsAndRestrictionsService->initialize();
        $io->success('Initialisation des règles et restrictions de maison terminée avec succès.');

        $io->section('Initialisation des status d\'échange');
        $this->exchangeStatusService->initialize();
        $io->success('Initialisation des status d\'échange terminée avec succès.');

        $io->section('Initialisation des critères de notation');
        $this->notationCriteriaService->initialize();
        $io->success('Initialisation des critères de notation terminée avec succès.');

        $io->section('Initialisation des types de maison');
        $this->homeTypeService->initialize();
        $io->success('Initialisation des types de maison terminée avec succès.');

        $io->section('Initialisation des niveaux de sol');
        $this->floorLevelService->initialize(); // <-- Appel de la méthode pour initialiser les niveaux de sol
        $io->success('Initialisation des niveaux de sol terminée avec succès.');

        $io->section('Initialisation des types de parking et garage');
        $this->homeTypeOfParkingAndGarageService->initialize(); // <-- Appel de la méthode pour initialiser les types de parking et garage
        $io->success('Initialisation des types de parking et garage terminée avec succès.');

        $io->success('Iniatialisation fait avec succès.');

        return Command::SUCCESS;
    }
}