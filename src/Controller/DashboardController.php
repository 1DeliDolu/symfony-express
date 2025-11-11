<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\PublisherRepository;
use App\Repository\SaleRepository;
use App\Repository\TitleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly TitleRepository $titleRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly PublisherRepository $publisherRepository,
        private readonly SaleRepository $saleRepository,
    ) {}

    #[Route('/admin', name: 'app_dashboard')]
    public function index(): Response
    {
        // Redirect to database selection page
        return $this->redirectToRoute('app_database_selection');
    }

    #[Route('/admin/pubs', name: 'app_dashboard_pubs')]
    public function pubsDashboard(): Response
    {
        // Get statistics for dashboard
        $totalTitles = count($this->titleRepository->findAll());
        $totalAuthors = count($this->authorRepository->findAll());
        $totalPublishers = count($this->publisherRepository->findAll());
        $totalSales = count($this->saleRepository->findAll());

        // Get recent titles (last 6)
        $recentTitles = $this->titleRepository->findBy([], ['pubdate' => 'DESC'], 6);

        // Get all sales for recent activity
        $recentSales = $this->saleRepository->findBy([], ['ordDate' => 'DESC'], 10);

        return $this->render('dashboard/pubs.html.twig', [
            'totalTitles' => $totalTitles,
            'totalAuthors' => $totalAuthors,
            'totalPublishers' => $totalPublishers,
            'totalSales' => $totalSales,
            'recentTitles' => $recentTitles,
            'recentSales' => $recentSales,
        ]);
    }
}
