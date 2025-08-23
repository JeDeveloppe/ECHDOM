<?php

namespace App\Controller\Site;

use App\Entity\Home;
use App\Entity\Workplace;
use App\Form\HomeType;
use App\Form\UserWorkplaceChoiceType;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user' , name: 'user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{

    public function __construct(
        private GeocodingService $geocodingService,
        private EntityManagerInterface $em,
    )
    {
    }

    #[Route('/', name: '_dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $workplace = $user->getWorkplaces()->first() ?: new Workplace();

        $workplaceForm = $this->createForm(UserWorkplaceChoiceType::class, $workplace);
        $workplaceForm->handleRequest($request);

        // Cette méthode gère le cas où le formulaire est soumis de manière "traditionnelle"
        // (non-AJAX), ce qui ne devrait pas arriver avec notre implémentation front-end
        // mais est une bonne pratique.
        if ($workplaceForm->isSubmitted() && $workplaceForm->isValid()) {
            $workplace->setOwner($user);
            $entityManager->persist($workplace);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu de travail mis à jour.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('site/user/dashboard.html.twig', [
            'workplaceForm' => $workplaceForm->createView(),
        ]);
    }

    /**
     * Endpoint API pour la recherche d'adresses avec le service de géocodage.
     * Appelé par le JavaScript pour l'autocomplétion.
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/geocode', name: 'api_geocode', methods: ['GET'])]
    public function geocode(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        if (!$query) {
            return new JsonResponse(['error' => 'La requête doit contenir un paramètre "q".'], Response::HTTP_BAD_REQUEST);
        }

        $results = $this->geocodingService->searchAddress($query);

        return new JsonResponse($results);
    }

    #[Route('/api/workplace/update', name: 'app_workplace_update', methods: ['POST'])]
    public function updateWorkplace(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $workplace = $user->getWorkplaces()->first() ?: new Workplace();

        $data = json_decode($request->getContent(), true);

        // Crée le formulaire et le soumet avec les données JSON
        $form = $this->createForm(UserWorkplaceChoiceType::class, $workplace);
        $form->submit($data, true);

        if ($form->isSubmitted() && $form->isValid()) {
            $workplace->setOwner($user);
            $entityManager->persist($workplace);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'address' => $workplace->getAddress()]);
        }

        // Si le formulaire n'est pas valide, renvoie les erreurs
        $errors = [];
        foreach ($form->getErrors(true, false) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse(['error' => implode(', ', $errors)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * route vers la page du formulaire pour mettre à jour créer un Home
     */
    #[Route('/mon-bien-a-l-echange', name: '_my_property_in_exchange', methods: ['GET', 'POST'])]
    public function myPropertyInExchange(Request $request): Response
    {
        $home = $this->getUser()->getHomes()->first() ?: new Home();

        $isEditMode = $home->getId() !== null; // Détermine si on est en mode édition

        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $home->setOwner($this->getUser());
            if( $home->getTypeOfGarage()->getName() === 'Aucun' ) {
                $home->setHasGarage(false);
            }
            if( $home->getTypeOfParking()->getName() === 'Aucun' ) {
                $home->setHasParking(false);
            }

            $this->em->persist($home);
            $this->em->flush();

            $this->addFlash('success', 'Votre bien a été ajouté avec succès !');
            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('site/user/my_property_in_exchange.html.twig', [
            'form' => $form->createView(),
            'isEditMode' => $isEditMode,
        ]);
    }
}
