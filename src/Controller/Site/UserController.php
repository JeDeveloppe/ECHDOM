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
    #[Route('/mon-bien-a-l-echange', name: '_my_property_in_exchange', methods: ['GET', 'POST'])]
    public function myPropertyInExchange(Request $request): Response
    {
        $home = $this->getUser()->getHomes()->first() ?: new Home();

        $isEditMode = $home->getId() !== null; // Détermine si on est en mode édition

        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        $availabilityForm = $this->createForm(HomeAvailabilityFormType::class);
        $availabilityForm->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

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
        }

        if($availabilityForm->isSubmitted() && $availabilityForm->isValid()) {
            // Récupère les données du formulaire
            $startAt = $availabilityForm->get('startAt')->getData();
            $endAt = $availabilityForm->get('endAt')->getData();
            $weeklyDays = $availabilityForm->get('weeklyDays')->getData();

            // S'assure que la date de fin est postérieure ou égale à la date de début
            if ($startAt > $endAt) {
                $this->addFlash('error', 'La date de début ne peut pas être postérieure à la date de fin.');
                return $this->redirectToRoute('_my_property_in_exchange');
            }

            // Crée un itérateur pour parcourir chaque jour de la période
            $period = new DatePeriod(
                $startAt,
                new DateInterval('P1D'), // Incrémente de 1 jour
                $endAt->modify('+1 day') // S'arrête à la date de fin incluse
            );

            // Définit le fuseau horaire pour la France
            $timezone = new DateTimeZone('Europe/Paris');
            $now = new \DateTimeImmutable('now', $timezone);

            // Parcourt chaque jour de la période
            foreach ($period as $date) {
                // Le format 'N' renvoie le jour de la semaine (1 pour lundi, 7 pour dimanche)
                $dayOfWeek = (int) $date->format('N');

                // Si le jour de la semaine est dans le tableau des jours cochés
                if (in_array($dayOfWeek, $weeklyDays)) {
                    // Crée une NOUVELLE instance de l'entité HomeAvailability pour ce jour
                    $newAvailability = new HomeAvailability();
                    $immutableDate = DateTimeImmutable::createFromMutable($date);
                    
                    // Définit la date de début avec l'heure à 12:00:00 et le fuseau horaire
                    $startDateTime = $immutableDate->setTime(12, 0, 0)->setTimezone($timezone);
                    $newAvailability->setStartAt($startDateTime);
                    
                    // Définit la date de fin (pour un seul jour)
                    $endDateTime = $immutableDate->setTime(23, 59, 59)->setTimezone($timezone);
                    $newAvailability->setEndAt($endDateTime); 
                    
                    // Ajoute l'entité HomeAvailability au Home
                    $myHome = $this->getUser()->getHomes()->first();
                    $newAvailability->setHome($myHome); 
                    $newAvailability->setCreatedAt($now);

                    $this->em->persist($newAvailability);
                }
            }

            // Exécute toutes les requêtes d'insertion en une seule transaction
            $this->em->flush();

            $this->addFlash('success', 'Vos disponibilités ont été enregistrées avec succès !');

            return $this->redirectToRoute('_my_property_in_exchange');
        }

        $homeAvailabilities = $this->homeAvailabilityRepository->findBy(['home' => $home], ['startAt' => 'ASC']); 

        return $this->render('site/user/my_property_in_exchange.html.twig', [
            'form' => $form->createView(),
            'isEditMode' => $isEditMode,
            'availabilityForm' => $availabilityForm->createView(),
            'homeAvailabilities' => $homeAvailabilities,
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
