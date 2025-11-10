<?php

namespace App\Controller;

use App\Repository\TitleRepository;
use App\Repository\AuthorRepository;
use App\Repository\PublisherRepository;
use App\Repository\StoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        TitleRepository $titleRepository,
        AuthorRepository $authorRepository,
        PublisherRepository $publisherRepository,
        StoreRepository $storeRepository
    ): Response
    {
        // Get statistics
        $totalTitles = count($titleRepository->findAll());
        $totalAuthors = count($authorRepository->findAll());
        $totalPublishers = count($publisherRepository->findAll());
        $totalStores = count($storeRepository->findAll());

        // Get recent items (limit 6)
        $recentTitles = $titleRepository->findBy([], ['pubdate' => 'DESC'], 6);
        $recentAuthors = $authorRepository->findBy([], ['auLname' => 'ASC'], 6);
        $featuredPublishers = $publisherRepository->findAll();

        return $this->render('home/index.html.twig', [
            'totalTitles' => $totalTitles,
            'totalAuthors' => $totalAuthors,
            'totalPublishers' => $totalPublishers,
            'totalStores' => $totalStores,
            'recentTitles' => $recentTitles,
            'recentAuthors' => $recentAuthors,
            'featuredPublishers' => $featuredPublishers,
        ]);
    }
}
