<?php

namespace App\Controller\Site;

use App\Service\MapsService;
use App\Form\SearchHomesType;
use App\Repository\HomeRepository;
use App\Service\LengthAndTimeTravelService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(
        private  LengthAndTimeTravelService $lengthAndTimeTravelService,
        private Security $security,
        private MapsService $mapsService,
        private HomeRepository $homeRepository
    )
    {
    }
    #[Route('/trouvez-un-logement-pres-de-votre-travail', name: 'site_homes_nearby_workplace')]
    public function searchMyNearbyHomes(Request $request): Response
    {

        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('site_login');
        }

        //?on récupère le lieu de travail de l'utilisateur
        $workplaces = $user->getWorkplaces();
        if (!$workplaces) {
            $this->addFlash('error', 'Vous devez d\'abord créer un lieu de travail.');
            return $this->redirectToRoute('app_accueil');
        }

        //?on calcule le temps de trajet entre le lieu de travail et la maison de l'utilisateur
        $timeBetweenmMyHomeAndMyWorkplace = $this->lengthAndTimeTravelService->getDistancesBeetweenTwoGpsPoints(
            $user->getHomes()->first(),
            $workplaces->first()
        );

        //?on va ajouter le formulaire de recherche de maisons
        $form = $this->createForm(SearchHomesType::class, null, [
            'timeBetweenmMyHomeAndMyWorkplace' => $timeBetweenmMyHomeAndMyWorkplace,
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //?on récupère les données du formulaire
            $datas = $form->getData();
            $duration = $datas['duration'];

            //?on récupère les maisons à proximité du lieu de travail
            $homesNearMyWorkplace = $this->lengthAndTimeTravelService->findNearbyHomesByTravelTime($workplaces->first(), $duration, $distance = 20);
        }

        if(empty($homesNearMyWorkplace)) {
           $maps = null;
           $homes = null;
        }else{

            //?on va generer une mini carte par résultat
            $homes = [];
            foreach($homesNearMyWorkplace as $home){
                $map = $this->mapsService->generateMapWithOneTypeOfPlace($home, 'homes');
                //?on ajoute le lieu de résidence de l'utilisateur à la carte
                $map = $this->mapsService->addMarkerToMap($map, $user->getHomes()->first(), 'homes', 'warning');
                //?on ajoute le lieu de travail de l'utilisateur à la carte
                $map = $this->mapsService->addMarkerToMap($map, $user->getWorkplaces()->first(), 'workplaces', 'danger');
                $homes[] = [
                    'details' => [
                        'id' => $home->getId(),
                        'equipments' => $home->getEquipments()->toArray(),
                        'home' => $home,
                        'timeTravelBetweenHomeAndWorkplace' => $home->getTimeTravelBetweenHomeAndWorkplace()
                    ],
                    'map' => $map,
                ];
            }

            // Tri du tableau par le temps de trajet ascendant
            usort($homes, function ($homeA, $homeB) {
                $timeA = $homeA['details']['timeTravelBetweenHomeAndWorkplace'];
                $timeB = $homeB['details']['timeTravelBetweenHomeAndWorkplace'];

                // Utilisation de l'opérateur de comparaison "spaceship" (disponible en PHP 7+)
                return $timeA <=> $timeB;
            });
        }

        return $this->render('site/homes/homes.html.twig', [
            'homes' => $homes,
            'timeBetweenmMyHomeAndMyWorkplace' => $timeBetweenmMyHomeAndMyWorkplace,
            'form' => $form->createView(),
            'disable_turbo' => true, // Désactive Turbo pour cette page
        ]);
    }

    #[Route('/api/home/{id}', name: 'api_home_details', methods: ['GET'])]
    public function getHomeDetails(int $id, HomeRepository $homeRepository, SerializerInterface $serializer): JsonResponse
    {
        $home = $homeRepository->find($id);

        if (!$home) {
            return new JsonResponse(['message' => 'Logement non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Sérialisation des données du logement avec les groupes de sérialisation
        $jsonContent = $serializer->serialize($home, 'json', ['groups' => 'home:details']);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }
}
