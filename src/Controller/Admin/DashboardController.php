<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Commande;
use App\Entity\Livres;
use App\Entity\User;
use App\Repository\AchatRepository;
use App\Repository\CommandeRepository;
use App\Repository\CategoriesRepository;
use App\Repository\LivresRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UserRepository $userRepository,
        private LivresRepository $livresRepository,
        private CategoriesRepository $categoriesRepository,
        private CommandeRepository $commandeRepository,
        private AchatRepository $achatRepository,
        private ChartBuilderInterface $chartBuilder
    ) {
    }

    public function LivresPlusVenduCategorie(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['Livres', 'Categories'],
            'datasets' => [
                [
                    'label' => 'Livres Plus Vendu',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10],
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return  $chart;
    }

    public function CategoriesPlusVendu(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['Livres', 'Categories'],
            'datasets' => [
            [
                'label' => 'Categories Plus Vendu',
                'backgroundColor' => 'rgb(255, 99, 132)',
                'borderColor' => 'rgb(255, 99, 132)',
                'data' => [0, 10],
            ],
            ],
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $topLivres = $this->achatRepository->OccurrencesLivres();
        $topCategories = $this->achatRepository->OccurrencesCategorie();
        $nombreCommandes = $this->commandeRepository->nombreCommande();
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        return $this->render('dashboard/index.html.twig', [
            'topLivres' => $topLivres,
            'topCategories' => $topCategories,
            'nombreCommandes' => $nombreCommandes,
            'chart' => $chart,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new() ->setTitle('Symbook');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Livres', 'fa fa-book', Livres::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-book', Categories::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-book', User::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-book', Commande::class);
    }
}
