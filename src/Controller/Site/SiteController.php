<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SiteController extends AbstractController
{
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
}
