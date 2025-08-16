<?php

namespace App\Command;

use App\Service\HomeEquipmentService;
use App\Service\HomeRegulationsAndRestrictionsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:initialize-equipment',
    description: 'Crée les équipements de maison initiaux dans la base de données.'
)]
class InitAppCommand extends Command
{
    public function __construct(
        private HomeEquipmentService $homeEquipmentService,
        private HomeRegulationsAndRestrictionsService $homeRegulationsAndRestrictionsService,
    )
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Appelle le service pour initialiser la base de données
        $this->homeEquipmentService->initializeHomeEquipments($io);
        $this->homeRegulationsAndRestrictionsService->initializeHomeRegulationsAndRestrictions($io);

        $io->success('Iniatialisation fait avec succès.');

        return Command::SUCCESS;
    }
}