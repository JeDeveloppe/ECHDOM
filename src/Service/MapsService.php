<?php

namespace App\Service;

use Symfony\UX\Map\Map;
use Symfony\UX\Map\Point;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Icon\Icon;
use Symfony\UX\Map\InfoWindow;

class MapsService
{
    public function __construct()
    {
    }


    /**
     * Récupère une adresse aléatoire pour un pays donné.
     *
     * @param array $places Les données des lieux.
     * @param string $typeOfPlace Type de lieu (par exemple, "workplaces, users").
     * @return Map
     */
    public function generateMapWithAllDatabaseObjects(array $places, string $typeOfPlace): Map
    {
        switch ($typeOfPlace) {
            case 'workplaces':
                $icon = Icon::ux('vaadin:workplace')->width(24)->height(24);
                break;
            case 'homes':
                $icon = Icon::ux('fa:home')->width(24)->height(24);
                break;
            default:
                $icon = Icon::ux('carbon:unknown-filled')->width(8)->height(8);
                
        }

        
        $map = (new Map('default'))
            ->fitBoundsToMarkers()
            ->minZoom(4)
            ->zoom(4);
        
        //? On ajoute un marqueur pour chaque lieu
        foreach ($places as $place) {
            $map->addMarker(new Marker(
                position: new Point($place->getLatitude(), $place->getLongitude()),
                title: $place->getAddress(),
                icon: $icon,
                infoWindow: new InfoWindow(
                    content: '<p>' . $place->getAddress() . '</p>',
                )
            ));
        }

        return $map;
    }

}