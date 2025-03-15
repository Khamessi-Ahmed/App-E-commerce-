<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(LivresRepository $livresRepository): Response
    {
        $top4livres = $livresRepository->getLastFourLivres();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'toplivres' => $top4livres,
        ]);
    }
}
