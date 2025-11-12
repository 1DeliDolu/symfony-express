<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Pubs\AuthorRepository;
use App\Repository\Pubs\PublisherRepository;
use App\Repository\Pubs\StoreRepository;
use App\Repository\Pubs\TitleRepository;
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
    ) {}

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        // Get statistics
        $totalTitles = count($this->titleRepository->findAll());
        $totalAuthors = count($this->authorRepository->findAll());
        $totalPublishers = count($this->publisherRepository->findAll());
        $totalStores = count($this->storeRepository->findAll());

        // Get recent items (limit 6)
        $recentTitles = $this->titleRepository->findBy([], ['pubdate' => 'DESC'], 6);
        $recentAuthors = $this->authorRepository->findBy([], ['auLname' => 'ASC'], 6);
        $featuredPublishers = $this->publisherRepository->findAll();

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
