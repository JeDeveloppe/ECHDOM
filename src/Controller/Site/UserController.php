<?php

namespace App\Controller\Site;

use DatePeriod;
use DateInterval;
use DateTimeZone;
use App\Entity\Home;
use App\Form\HomeType;
use DateTimeImmutable;
use App\Entity\Workplace;
use App\Entity\HomeAvailability;
use App\Service\GeocodingService;
use App\Form\UserWorkplaceChoiceType;
use App\Form\HomeAvailabilityFormType;
use App\Repository\HomeAvailabilityRepository;
use App\Repository\HomeRepository;
use App\Service\HomeAvailabilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user' , name: 'user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{

    public function __construct(
        private GeocodingService $geocodingService,
        private EntityManagerInterface $em,
        private HomeRepository $homeRepository,
        private HomeAvailabilityService $homeAvailabilityService,
        private HomeAvailabilityRepository $homeAvailabilityRepository
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

    #[Route('/mes-biens', name: '_homes', methods: ['GET'])]
    public function listUserHomes(): Response
    {
        // Récupère la collection de tous les biens immobiliers de l'utilisateur
        $homes = $this->getUser()->getHomes();

        return $this->render('site/user/list_homes.html.twig', [
            'homes' => $homes,
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

    #[Route('/api/workplace/update', name: 'api_workplace_update', methods: ['POST'])]
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

    #[Route('/mon-bien-a-l-echange/{id}', name: '_my_property_in_exchange', methods: ['GET', 'POST'])]
    public function myPropertyInExchange(Request $request, int $id): Response
    {
        /** @var Home $home */
        $home = $this->homeRepository->findOneBy(['id' => $id, 'owner' => $this->getUser()]);

        if (!$home) {
            $this->addFlash('warning', 'Bien immobilier non trouvé.');
            return $this->redirectToRoute('user_home_list');
        }

        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        $availabilityForm = $this->createForm(HomeAvailabilityFormType::class);
        $availabilityForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Les informations du bien ont été mises à jour !');
        }

        if ($availabilityForm->isSubmitted() && $availabilityForm->isValid()) {
            $startAt = $availabilityForm->get('startAt')->getData();
            $endAt = $availabilityForm->get('endAt')->getData();
            $weeklyDays = $availabilityForm->get('weeklyDays')->getData();

            $startAtImmutable = \DateTimeImmutable::createFromMutable($startAt);
            $endAtImmutable = \DateTimeImmutable::createFromMutable($endAt);
            
            // Appel du service pour gérer la logique de disponibilité
            $this->homeAvailabilityService->handleAvailability($home, $startAtImmutable, $endAtImmutable, $weeklyDays);

            $this->addFlash('success', 'Vos disponibilités ont été enregistrées avec succès !');

            return $this->redirectToRoute('user_my_property_in_exchange', ['id' => $home->getId()]);
        }

        return $this->render('site/user/my_property_in_exchange.html.twig', [
            'form' => $form->createView(),
            'availabilityForm' => $availabilityForm->createView(),
            'isEditMode' => true,
            'home' => $home,
        ]);
    }

    /**
     * @Route("/home/{id}/availability", name="app_home_add_availability", methods={"POST"})
     */
    #[Route('/api/home/{id}/availability', name: 'api_home_add_availability', methods: ['POST'])]
    public function addAvailability(Request $request, Home $home, EntityManagerInterface $entityManager): Response
    {
        // On crée une instance de notre nouveau formulaire
        $form = $this->createForm(HomeAvailabilityFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['isRecurring']) {
                // Logique pour la récurrence
                $endDate = $data['recurrenceEndAt'];
                $weeklyDays = $data['weeklyDays'];
                $recurrenceType = $data['recurrenceType'];
                
                $currentDate = new \DateTime();

                while ($currentDate <= $endDate) {
                    if ($recurrenceType === 'weekly' && in_array($currentDate->format('w'), $weeklyDays)) {
                        $availability = new HomeAvailability();
                        $availability->setHome($home);
                        $availability->setStartAt(\DateTimeImmutable::createFromMutable($currentDate));
                        $availability->setEndAt(\DateTimeImmutable::createFromMutable($currentDate));
                        $entityManager->persist($availability);
                    }
                    // TODO: Implémenter la logique pour le type 'monthly'
                    
                    // Incrémenter la date
                    $currentDate->modify('+1 day');
                }
            } else {
                // Logique pour une date unique
                $availability = new HomeAvailability();
                $availability->setHome($home);
                $availability->setStartAt(\DateTimeImmutable::createFromMutable($data['startAt']));
                $availability->setEndAt(\DateTimeImmutable::createFromMutable($data['endAt']));
                $entityManager->persist($availability);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Vos disponibilités ont bien été ajoutées !');
            return $this->redirectToRoute('app_home_edit', ['id' => $home->getId()]);
        }

        // Si le formulaire n'est pas valide, on peut retourner une erreur
        // (bien que le JS devrait l'empêcher pour l'utilisateur final)
        $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout des disponibilités.');
        return $this->redirectToRoute('app_home_edit', ['id' => $home->getId()]);
    }
}
