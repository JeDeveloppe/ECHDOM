<?php

namespace App\Controller\Admin;

use App\Repository\HomeRepository;
use App\Repository\WorkplaceRepository;
use App\Service\MapsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MapController extends AbstractController
{
    public function __construct(
        private WorkplaceRepository $workplaceRepository,
        private HomeRepository $homeRepository,
        private MapsService $mapsService,
    )
    {
    }

    #[Route('admin/maps/all-workplaces', name: 'admin_map_all_workplaces')]
    public function mapsAllWorkplaces(): Response
    {
        $workplaces = $this->workplaceRepository->findAll();
        $map = $this->mapsService->generateMapWithAllDatabaseObjects($workplaces,"workplaces");

        return $this->render('admin/map/map_all_places.html.twig', [
            'map' => $map,
            'h1' => 'Tous les lieux de travail',
        ]);
    }

    #[Route('admin/maps/all-homes', name: 'admin_map_all_homes')]
    public function mapsAllHomes(): Response
    {
        $homes = $this->homeRepository->findAll();
        $map = $this->mapsService->generateMapWithAllDatabaseObjects($homes,"homes");

        return $this->render('admin/map/map_all_places.html.twig', [
            'map' => $map,
            'h1' => 'Tous les lieux de résidence',
        ]);
    }
}
