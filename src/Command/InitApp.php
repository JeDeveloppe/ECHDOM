<?php

namespace App\Command;

use App\Service\ExchangeStatusService;
use App\Service\HomeEquipmentService;
use App\Service\HomeRegulationsAndRestrictionsService;
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
    )
    {
         parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Appelle le service pour initialiser la base de données
        $this->homeEquipmentService->initializeHomeEquipments($io);
        $this->homeRegulationsAndRestrictionsService->initializeHomeRegulationsAndRestrictions($io);
        $this->exchangeStatusService->initializeExchangeStatus($io);
        $this->notationCriteriaService->initializeNotationCriterias($io);
        $this->homeTypeService->initializeNotationCriterias($io);

        $io->success('Iniatialisation fait avec succès.');

        return Command::SUCCESS;
    }
}