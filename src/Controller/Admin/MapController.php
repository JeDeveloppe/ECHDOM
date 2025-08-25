<?php

namespace App\Controller\Admin;

use App\Service\MapsService;
use App\Repository\PropertyRepository;
use App\Repository\WorkplaceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MapController extends AbstractController
{
    public function __construct(
        private WorkplaceRepository $workplaceRepository,
        private PropertyRepository $propertyRepository,
        private MapsService $mapsService,
    )
    {
    }

    #[Route('admin/maps/all-workplaces', name: 'admin_map_all_workplaces')]
    public function mapsAllWorkplaces(): Response
    {
        $maps = [];
        $workplaces = $this->workplaceRepository->findAll();
        $maps['workplaces'] = $this->mapsService->generateMapWithAllDatabaseObjects($workplaces,"workplaces");

        return $this->render('admin/map/map_all_places.html.twig', [
            'maps' => $maps,
            'h1' => 'Tous les lieux de travail',
        ]);
    }

    #[Route('admin/maps/all-properties', name: 'admin_map_all_properties')]
    public function mapsAllProperties(): Response
    {
        $maps = [];
        $properties = $this->propertyRepository->findAll();
        $maps['properties'] = $this->mapsService->generateMapWithAllDatabaseObjects($properties,"properties");

        return $this->render('admin/map/map_all_places.html.twig', [
            'maps' => $maps,
            'h1' => 'Toutes les propriétés',
        ]);
    }

    #[Route('admin/maps/all-properties-and-workplaces', name: 'admin_map_all_properties_and_workplaces')]
    public function mapsAllPropertiesAndWorkplaces(): Response
    {
        $maps = [];

        $properties = $this->propertyRepository->findAll();
        $maps['properties'] = $this->mapsService->generateMapWithAllDatabaseObjects($properties,"properties");

        $workplaces = $this->workplaceRepository->findAll();
        $maps['workplaces'] = $this->mapsService->generateMapWithAllDatabaseObjects($workplaces,"workplaces");

        return $this->render('admin/map/map_all_places.html.twig', [
            'maps' => $maps,
            'h1' => 'Toutes les propriétés et les lieux de travail',
        ]);
    }
}
