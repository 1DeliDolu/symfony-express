<?php

namespace App\Controller;

use App\Repository\TitleRepository;
use App\Repository\AuthorRepository;
use App\Repository\PublisherRepository;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        TitleRepository $titleRepository,
        AuthorRepository $authorRepository,
        PublisherRepository $publisherRepository,
        SaleRepository $saleRepository
    ): Response
    {
        // Get statistics for dashboard
        $totalTitles = count($titleRepository->findAll());
        $totalAuthors = count($authorRepository->findAll());
        $totalPublishers = count($publisherRepository->findAll());
        $totalSales = count($saleRepository->findAll());

        // Get recent titles (last 6)
        $recentTitles = $titleRepository->findBy([], ['pubdate' => 'DESC'], 6);

        // Get all sales for recent activity
        $recentSales = $saleRepository->findBy([], ['ordDate' => 'DESC'], 10);

        return $this->render('dashboard/index.html.twig', [
            'totalTitles' => $totalTitles,
            'totalAuthors' => $totalAuthors,
            'totalPublishers' => $totalPublishers,
            'totalSales' => $totalSales,
            'recentTitles' => $recentTitles,
            'recentSales' => $recentSales,
        ]);
    }
}
