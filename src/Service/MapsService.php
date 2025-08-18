<?php

namespace App\Service;

use App\Entity\GeolocatableInterface;
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
     * @param ?string $color Couleur de l'icône (optionnel).
     * @return Map
     */
    public function addMarkerToMap(Map $map, GeolocatableInterface $geolocatable, string $typOfPlace, ?string $color = null): Map
    {
        $icon = $this->getIconByType($typOfPlace, $color);

        $latitude = $geolocatable->getLatitude();
        $longitude = $geolocatable->getLongitude();
        $address = $geolocatable->getAddress();

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
     * @param ?string $color Couleur de l'icône (optionnel).
     * @return Icon
     */
    private function getIconByType(string $typeOfPlace, ?string $color = null): Icon
    {
        $options = [
            'width' => 24,
            'height' => 24,
        ];

        if ($color !== null) {
            // Bootstrap colors: primary, success, danger, warning, info, etc.
            if (in_array($color, ['primary', 'success', 'danger', 'warning', 'info', 'secondary', 'dark', 'light', 'muted', 'white'])) {
                $options['class'] = 'text-' . $color;
            } else {
                // Custom CSS color
                $options['style'] = 'color: ' . $color . ';';
            }
        } else {
            $options['class'] = 'text-primary';
        }

        switch ($typeOfPlace) {
            case 'workplaces':
                return Icon::ux('vaadin:workplace', $options);
            case 'homes':
                return Icon::ux('fa:home', $options);
            default:
                return Icon::ux('carbon:unknown-filled', array_merge($options, [
                    'width' => 8,
                    'height' => 8,
                ]));
        }
    }

}