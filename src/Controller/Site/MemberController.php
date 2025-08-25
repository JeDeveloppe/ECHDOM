<?php

namespace App\Controller\Site;

use DateTimeImmutable;
use App\Entity\Workplace;
use App\Form\PropertyType;
use App\Form\FullAddressType;
use App\Service\GeocodingService;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PropertyAvailabilityFormType;
use App\Service\PropertyAvailabilityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PropertyAvailabilityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/member' , name: 'member')]
#[IsGranted('ROLE_USER')]
class MemberController extends AbstractController
{

    public function __construct(
        private GeocodingService $geocodingService,
        private EntityManagerInterface $em,
        private PropertyRepository $propertyRepository,
        private PropertyAvailabilityService $propertyAvailabilityService,
        private PropertyAvailabilityRepository $propertyAvailabilityRepository
    )
    {
    }

    #[Route('/', name: '_dashboard')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        // L'analyseur est maintenant informé que l'objet est de type App\Entity\User
        $workplace =  $user->getWorkplaces()->first() ?: new Workplace();

        $workplaceForm = $this->createForm(FullAddressType::class, $workplace);
        $workplaceForm->handleRequest($request);

        // Cette méthode gère le cas où le formulaire est soumis de manière "traditionnelle"
        // (non-AJAX), ce qui ne devrait pas arriver avec notre implémentation front-end
        // mais est une bonne pratique.
        if ($workplaceForm->isSubmitted() && $workplaceForm->isValid()) {
            $workplace->setOwner($user);
            $entityManager->persist($workplace);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu de travail mis à jour.');
            return $this->redirectToRoute('member_dashboard');
        }

        return $this->render('member/dashboard.html.twig', [
            'workplaceForm' => $workplaceForm->createView(),
        ]);
    }

    #[Route('/propriete/ajouter', name: '_new_property', methods: ['GET', 'POST'])]
    public function newProperty(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $propertyForm = $this->createForm(PropertyType::class);
        $propertyForm->handleRequest($request);

        if ($propertyForm->isSubmitted() && $propertyForm->isValid()) {
            $property = $propertyForm->getData();
            $property->setOwner($user);
            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'Bien immobilier créé avec successe !');
            return $this->redirectToRoute('member_properties');
        }

        return $this->render('member/property/new_property.html.twig', [
            'propertyForm' => $propertyForm->createView(),
            'isEditMode' => false,
            'disable_turbo' => true, // Désactive Turbo pour ce formulaire
        ]);
    }

    #[Route('/propriete/liste', name: '_properties', methods: ['GET'])]
    public function userProperties(): Response
    {
        // Récupère la collection de tous les biens immobiliers de l'utilisateur
        $properties = $this->getUser()->getProperties();

        return $this->render('member/property/properties.html.twig', [
            'properties' => $properties,
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
        $form = $this->createForm(FullAddressType::class, $workplace);
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
     * route vers la page du formulaire pour mettre à jour créer une proprieté
     */

    #[Route('/propriete/{id}/details', name: 'property_details', methods: ['GET', 'POST'])]
    public function myPropertyInExchange(Request $request, int $id): Response
    {

        $property = $this->propertyRepository->findOneBy(['id' => $id, 'owner' => $this->getUser()]);

        if (!$property) {
            $this->addFlash('warning', 'Propriété non trouvé.');
            return $this->redirectToRoute('member_properties');
        }

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        $availabilityForm = $this->createForm(PropertyAvailabilityFormType::class);
        $availabilityForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Les informations ont été mises à jour !');
        }

        if ($availabilityForm->isSubmitted() && $availabilityForm->isValid()) {
            $startAt = $availabilityForm->get('startAt')->getData();
            $endAt = $availabilityForm->get('endAt')->getData();
            $weeklyDays = $availabilityForm->get('weeklyDays')->getData();

            $startAtImmutable = \DateTimeImmutable::createFromMutable($startAt);
            $endAtImmutable = \DateTimeImmutable::createFromMutable($endAt);

             // --- VALIDATION CÔTÉ SERVEUR SUPPLÉMENTAIRE ---
            // Vérifier que les dates ne sont pas un vendredi (5), un samedi (6) ou un dimanche (0)
            if ($startAt->format('w') == 5 || $startAt->format('w') == 6 || $startAt->format('w') == 0) {
                // Ajouter une erreur au formulaire si la date de début est invalide
                $this->addFlash('error', 'La date de début ne peut pas être un vendredi, un samedi ou un dimanche.');
                return $this->redirectToRoute('property_details', ['id' => $property->getId(), '_fragment' => 'availabilities']);
            }
            if ($endAt->format('w') == 5 || $endAt->format('w') == 6 || $endAt->format('w') == 0) {
                // Ajouter une erreur au formulaire si la date de fin est invalide
                $this->addFlash('error', 'La date de fin ne peut pas être un vendredi, un samedi ou un dimanche.');
                return $this->redirectToRoute('property_details', ['id' => $property->getId(), '_fragment' => 'availabilities']);
            }
            // --- FIN DE LA VALIDATION CÔTÉ SERVEUR ---
            
            // Appel du service pour gérer la logique de disponibilité
            $this->propertyAvailabilityService->handleAvailability($property, $startAtImmutable, $endAtImmutable, $weeklyDays);

            $this->addFlash('success', 'Vos disponibilités ont été enregistrées avec succès !');

            return $this->redirectToRoute('memberproperty_details', ['id' => $property->getId()]);
        }
        return $this->render('member/property/property_details.html.twig', [
            'propertyForm' => $form->createView(),
            'availabilityForm' => $availabilityForm->createView(),
            'isEditMode' => true,
            'property' => $property,
            'disable_turbo' => true, // Désactive Turbo pour ce formulaire
        ]);
    }
}
