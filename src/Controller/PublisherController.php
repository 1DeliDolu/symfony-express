<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Repository\PublisherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/publisher')]
#[IsGranted('ROLE_USER')]
final class PublisherController extends AbstractController
{
    public function __construct(
        private readonly PublisherRepository $publisherRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(name: 'app_publisher_index', methods: ['GET'])]
    public function index(): Response
    {
        $publishers = $this->publisherRepository->findAll();

        // Serialize publishers for Alpine.js
        $publishersData = array_map(function (Publisher $publisher) {
            return [
                'pubId' => $publisher->getPubId(),
                'pubName' => $publisher->getPubName() ?? 'N/A',
                'city' => $publisher->getCity() ?? 'N/A',
                'state' => $publisher->getState() ?? 'N/A',
                'country' => $publisher->getCountry() ?? 'USA',
            ];
        }, $publishers);

        return $this->render('publisher/index.html.twig', [
            'publishers' => $publishersData,
        ]);
    }

    #[Route('/new', name: 'app_publisher_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher, [
            'is_new' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($publisher);
            $this->entityManager->flush();

            $this->addFlash('success', 'Yayıncı başarıyla oluşturuldu.');

            return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publisher/new.html.twig', [
            'publisher' => $publisher,
            'form' => $form,
        ]);
    }

    #[Route('/{pubId}', name: 'app_publisher_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['pubId' => 'pubId'])] Publisher $publisher): Response
    {
        return $this->render('publisher/show.html.twig', [
            'publisher' => $publisher,
        ]);
    }

    #[Route('/{pubId}/edit', name: 'app_publisher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(mapping: ['pubId' => 'pubId'])] Publisher $publisher): Response
    {
        $form = $this->createForm(PublisherType::class, $publisher, [
            'is_new' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Yayıncı başarıyla güncellendi.');

            return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publisher/edit.html.twig', [
            'publisher' => $publisher,
            'form' => $form,
        ]);
    }

    #[Route('/{pubId}', name: 'app_publisher_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(mapping: ['pubId' => 'pubId'])] Publisher $publisher): Response
    {
        if ($this->isCsrfTokenValid('delete' . $publisher->getPubId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($publisher);
            $this->entityManager->flush();

            $this->addFlash('success', 'Yayıncı başarıyla silindi.');
        }

        return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
    }
}
