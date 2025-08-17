<?php

namespace App\Service;

use App\Entity\Home;
use App\Entity\HomeType;
use Dom\Entity;
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

        $icon = $this->getIconByType($typeOfPlace, 'primary');
        
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

    /**
     * Ajoute un marqueur spécifique à la carte.
     *
     * @param Map $map La carte à laquelle ajouter le marqueur.
     * @param Home $home L'entité contenant les informations du marqueur.
     * @param string $typeOfPlace Type de lieu (par exemple, "workplaces, users").
     * @return Map
     */
    public function addHomeMarkerFromUserToMap(Map $map, Home $home, string $typOfPlace): Map
    {
        $icon = $this->getIconByType($typOfPlace, 'danger');

        $latitude = $home->getLatitude();
        $longitude = $home->getLongitude();
        $address = $home->getAddress();

        $map->addMarker(new Marker(
            position: new Point($latitude, $longitude),
            title: $address,
            icon: $icon,
            infoWindow: new InfoWindow(
                content: '<p>' . $address . '</p>',
            )
        ));

        return $map;
    }

    /**
     * Retourne l'icône appropriée en fonction du type de lieu.
     *
     * @param string $typeOfPlace Le type de lieu (par exemple, "workplaces, homes").
     * @param string $color Couleur de l'icône (optionnel).
     * @return Icon
     */
    private function getIconByType(string $typeOfPlace, string $color): Icon
    {
        // On initialise les options de l'icône
        $options = [
            'width' => 24,
            'height' => 24,
        ];

        // Si une couleur est passée, on l'ajoute comme classe CSS
        if ($color) {
            $options['class'] = $color;
        }

        switch ($typeOfPlace) {
            case 'workplaces':
                $icon = Icon::ux('vaadin:workplace', $options);
                break;
            case 'homes':
                $icon = Icon::ux('fa:home', $options);
                break;
            default:
                $icon = Icon::ux('carbon:unknown-filled', [
                    'width' => 8,
                    'height' => 8,
                    'class' => $options['class'] ?? '',
                ]);
                break;
        }

        return $icon;
    }

}