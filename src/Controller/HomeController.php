<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Pubs\AuthorRepository;
use App\Repository\Pubs\PublisherRepository;
use App\Repository\Pubs\StoreRepository;
use App\Repository\Pubs\TitleRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly TitleRepository $titleRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly PublisherRepository $publisherRepository,
        private readonly StoreRepository $storeRepository,
        private readonly ManagerRegistry $registry,
    ) {}

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        // Pubs Database Statistics
        $pubsStats = [
            'totalTitles' => count($this->titleRepository->findAll()),
            'totalAuthors' => count($this->authorRepository->findAll()),
            'totalPublishers' => count($this->publisherRepository->findAll()),
            'totalStores' => count($this->storeRepository->findAll()),
            'recentTitles' => $this->titleRepository->findBy([], ['pubdate' => 'DESC'], 6),
        ];

        // Northwind Database Statistics
        $northwindStats = null;
        try {
            /** @var Connection $northwindConnection */
            $northwindConnection = $this->registry->getConnection('northwind');

            $totalOrders = $northwindConnection->executeQuery(
                "SELECT COUNT(*) as total FROM [northwind].[dbo].[Orders]"
            )->fetchOne();

            $totalProducts = $northwindConnection->executeQuery(
                "SELECT COUNT(*) as total FROM [northwind].[dbo].[Products]"
            )->fetchOne();

            $totalCustomers = $northwindConnection->executeQuery(
                "SELECT COUNT(*) as total FROM [northwind].[dbo].[Customers]"
            )->fetchOne();

            $totalEmployees = $northwindConnection->executeQuery(
                "SELECT COUNT(*) as total FROM [northwind].[dbo].[Employees]"
            )->fetchOne();

            $northwindStats = [
                'totalOrders' => $totalOrders,
                'totalProducts' => $totalProducts,
                'totalCustomers' => $totalCustomers,
                'totalEmployees' => $totalEmployees,
            ];
        } catch (\Exception $e) {
            // Northwind database not available
            $northwindStats = null;
        }

        // For backward compatibility, keep old variable names
        $totalTitles = $pubsStats['totalTitles'];
        $totalAuthors = $pubsStats['totalAuthors'];
        $totalPublishers = $pubsStats['totalPublishers'];
        $totalStores = $pubsStats['totalStores'];
        $recentTitles = $pubsStats['recentTitles'];

        return $this->render('home/index.html.twig', [
            'pubsStats' => $pubsStats,
            'northwindStats' => $northwindStats,
            // Backward compatibility
            'totalTitles' => $totalTitles,
            'totalAuthors' => $totalAuthors,
            'totalPublishers' => $totalPublishers,
            'totalStores' => $totalStores,
            'recentTitles' => $recentTitles,
        ]);
    }
}
