<?php

namespace App\Controller\Site;

use App\Service\LegalInformationService;
use App\Repository\LegalInformationRepository;
use App\Service\MentionsLegalesService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SiteController extends AbstractController
{
    public function __construct(
        private LegalInformationRepository $legalInformationRepository,
        private LegalInformationService $legal,
        private MentionsLegalesService $mentionsLegalesService
    )
    {
    }
    
    #[Route('/', name: 'site_accueil')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', []);
    }

    #[Route('/gains', name: 'site_gains')]
    public function gains(): Response
    {
        return $this->render('site/gains/gains.html.twig', []);
    }

    #[Route('/fonctionnalites', name: 'site_fonctionnalites')]
    public function fonctionnalites(): Response
    {
        return $this->render('site/fonctionalites/fonctionalites.html.twig', []);
    }

    #[Route('/mentions-legales', name: 'site_mentions_legales')]
    public function mentionsLegales(): Response
    {
        $legales = $this->legalInformationRepository->findOneBy([]);
        $paragraphs = $this->mentionsLegalesService->mentionsParagraphs($legales);
        $metas['description'] = 'Mentions légales du site.';

        return $this->render('site/legale/mentions_legales.html.twig', [
            'legales' => $legales,
            'metas' => $metas,
            'paragraphs' => $paragraphs
        ]);
    }
}
