<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/author')]
#[IsGranted('ROLE_USER')]
final class AuthorController extends AbstractController
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(name: 'app_author_index', methods: ['GET'])]
    public function index(): Response
    {
        $authors = $this->authorRepository->findAll();

        // Serialize authors for Alpine.js
        $authorsData = array_map(function (Author $author) {
            return [
                'auId' => $author->getAuId(),
                'auFname' => $author->getAuFname(),
                'auLname' => $author->getAuLname(),
                'phone' => $author->getPhone() ?? 'UNKNOWN',
                'address' => $author->getAddress() ?? 'N/A',
                'city' => $author->getCity() ?? 'N/A',
                'state' => $author->getState() ?? 'N/A',
                'zip' => $author->getZip() ?? 'N/A',
                'contract' => $author->isContract(),
            ];
        }, $authors);

        return $this->render('author/index.html.twig', [
            'authors' => $authorsData,
        ]);
    }

    #[Route('/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author, [
            'is_new' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($author);
            $this->entityManager->flush();

            $this->addFlash('success', 'Yazar başarıyla oluşturuldu.');

            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{auId}', name: 'app_author_show', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/{auId}/edit', name: 'app_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author, [
            'is_new' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Yazar başarıyla güncellendi.');

            return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{auId}', name: 'app_author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author): Response
    {
        if ($this->isCsrfTokenValid('delete' . $author->getAuId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($author);
            $this->entityManager->flush();

            $this->addFlash('success', 'Yazar başarıyla silindi.');
        }

        return $this->redirectToRoute('app_author_index', [], Response::HTTP_SEE_OTHER);
    }
}
