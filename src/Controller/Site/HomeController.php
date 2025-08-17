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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(
        private  LengthAndTimeTravelService $lengthAndTimeTravelService,
        private Security $security,
        private MapsService $mapsService,
    )
    {
    }
    #[Route('/homes', name: 'app_homes')]
    public function searchHomes(Request $request): Response
    {
        //?on va ajouter le formulaire de recherche de maisons
        $form = $this->createForm(SearchHomesType::class);
        $form->handleRequest($request);


        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        //?on récupère le lieu de travail de l'utilisateur
        $workplaces = $user->getWorkplaces();
        if (!$workplaces) {
            $this->addFlash('error', 'Vous devez d\'abord créer un lieu de travail.');
            return $this->redirectToRoute('app_accueil');
        }

        if($form->isSubmitted() && $form->isValid()){
            //?on récupère les données du formulaire
            $datas = $form->getData();
            $duration = $datas['duration'];
            $distance = $datas['distance'];

            //?on récupère les maisons à proximité du lieu de travail
            $homes = $this->lengthAndTimeTravelService->findNearbyHomesByTravelTime($workplaces->first(), $duration, $distance);
            dump($homes);
        }

        if(empty($homes)) {
           $map = null;
           $homes = null;
        }else{

            $map = $this->mapsService->generateMapWithAllDatabaseObjects($homes, 'homes');
            //?on ajoute le lieu de résidence de l'utilisateur à la carte
            $map = $this->mapsService->addHomeMarkerFromUserToMap($map, $user->getHomes()->first(), 'homes');

        }


        return $this->render('site/homes/homes.html.twig', [
            'map' => $map,
            'homes' => $homes,
            'form' => $form->createView(),
            'disable_turbo' => true, // Désactive Turbo pour cette page
        ]);
    }
}
