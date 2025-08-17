<?php

namespace App\Service;

use App\Entity\Home;
use App\Entity\Workplace;
use App\Repository\HomeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LengthAndTimeTravelService
{

    private const ROUTING_API_URL = 'https://api.tomtom.com/routing/1/calculateRoute/'; // Remplacez par votre API de routage

    public function __construct(
        private HomeRepository $homeRepository,
        private HttpClientInterface $client,
        private Security $security, // Ajout de la sécurité pour vérifier l'utilisateur
        )
    {
    }

    /**
     * Trouve les logements proches d'un lieu de travail en fonction du temps de trajet.
     *
     * @param Workplace $workplace Le lieu de travail de référence.
     * @param int $maxTravelTimeMinutes Le temps de trajet maximum en minutes.
     * @param int $radiusKm Le rayon initial de recherche en km.
     * @return array Les logements qui respectent le temps de trajet.
     */
    public function findNearbyHomesByTravelTime(Workplace $workplace, int $maxTravelTimeMinutes, int $radiusKm = 20): array
    {
        // 1. Première étape: filtrage initial par distance à vol d'oiseau pour réduire la charge.
        $nearbyHomes = $this->homeRepository->findHomesNearWorkplace(
            $workplace,
            $radiusKm
        );

        if(!$nearbyHomes) {
            return []; // Retourne un tableau vide si aucun logement n'est trouvé
        }

        $validHomes = [];

        // 2. Deuxième étape: filtrage par temps de trajet via une API externe.
        foreach ($nearbyHomes as $i => $home) {

            $travelTime = $this->getDistancesBeetweenTwoGpsPoints($home, $workplace);

            // Vérifie si le temps de trajet est valide et en dessous de la limite.
            if ($travelTime !== null && $travelTime <= $maxTravelTimeMinutes) {
                $validHomes[] = $home;
            }
            //?on ralenti les requêtes pour éviter de surcharger l'API
            if($i > 0 && $i % 5 == 0){
                sleep(1);
            }
        }

        return $validHomes;
    }

    public function getDistancesBeetweenTwoGpsPoints(Home $home, Workplace $workplace, ): ?int
    {

        $response = $this->client->request(
            'GET',
            self ::ROUTING_API_URL.$workplace->getLatitude().','.$workplace->getLongitude().':'.$home->getLatitude().','.$home->getLongitude().'/json?key='.$_ENV['TOMTOM_API_KEY']
        );

        $array_reponse = $response->toArray();
        dump($array_reponse);

        $duration = $array_reponse['routes'][0]['summary']['travelTimeInSeconds'];
        

        return $duration / 60; // Convertit en minutes
        // return $response;
    }
}