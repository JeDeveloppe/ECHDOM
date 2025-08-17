<?php

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function geocodeAddress(string $address): array
    {
        //?on définie des coordonnées par défaut
        $defaultCoordinates = [
            'address' => 'Default Address Paris',
            'y' => 48.8566, // Paris
            'x' => 2.3522, // Paris
        ];

        try {
            // Les paramètres de la requête basés sur votre commande curl
            $queryParams = [
                'q' => $address,
                'autocomplete' => 1,
                'index' => 'address',
                'limit' => 2,
                'returntruegeometry' => false,
            ];

            // Envoi de la requête GET
            $response = $this->client->request('GET', "https://data.geopf.fr/geocodage/search", [
                'query' => $queryParams,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            // Vérifie le code de statut de la réponse
            if ($response->getStatusCode() !== 200) {
                return [
                    'error' => 'La requête a échoué avec le code de statut : ' . $response->getStatusCode()
                ];
            }

            $datas = $response->toArray();

            if($datas['features']) {
                //?on récupère les coordonnées du premier résultat
                $coordinates = $datas['features'][0]['geometry']['coordinates'];
                $address = $datas['features'][0]['properties']['label'];
                return [
                    'address' => $address,
                    'y' => $coordinates[1],
                    'x' => $coordinates[0],
                ];
            }else {
                //?si aucun résultat n'est trouvé, on retourne les coordonnées par défaut
                return $defaultCoordinates;
            }

        } catch (Exception $e) {
            // Gère les exceptions de connexion ou de requête
            return [
                'error' => 'Une erreur est survenue lors de la requête : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Récupère une adresse aléatoire pour un pays donné.
     *
     * @param string $countryCode Le code pays (ex: 'fr' pour la France).
     * @return array Les données de l'adresse ou un tableau d'erreur.
     */
    public function getRandomAddress(string $countryCode = 'fr'): array
    {
        try {
            $response = $this->client->request('GET', "https://api.testingbot.com/v1/free-tools/random-address", [
                'query' => [
                    'country' => $countryCode,
                ],
            ]);

            // Vérifie si la requête a réussi (statut 200)
            if ($response->getStatusCode() !== 200) {
                return [
                    'error' => 'Erreur lors de la requête API',
                    'statusCode' => $response->getStatusCode(),
                ];
            }

            // Décode la réponse JSON en tableau PHP
            $addressData = $response->toArray();

            return $addressData;

        } catch (Exception $e) {
            // Gère les exceptions de connexion ou autres erreurs
            return [
                'error' => 'Une erreur est survenue',
                'message' => $e->getMessage(),
            ];
        }
    }
}