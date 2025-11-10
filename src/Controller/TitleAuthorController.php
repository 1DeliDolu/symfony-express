<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TitleAuthor;
use App\Form\TitleAuthorType;
use App\Repository\TitleAuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/title/author')]
final class TitleAuthorController extends AbstractController
{
    #[Route(name: 'app_title_author_index', methods: ['GET'])]
    public function index(TitleAuthorRepository $titleAuthorRepository): Response
    {
        $titleAuthors = $titleAuthorRepository->findAll();

        // Serialize title_authors for Alpine.js
        $titleAuthorsData = array_map(function (TitleAuthor $titleAuthor) {
            return [
                'titleId' => $titleAuthor->getTitle()->getTitleId(),
                'titleName' => $titleAuthor->getTitle()->getTitle(),
                'authorId' => $titleAuthor->getAuthor()->getAuId(),
                'authorName' => $titleAuthor->getAuthor()->getAuFname() . ' ' . $titleAuthor->getAuthor()->getAuLname(),
                'auOrd' => $titleAuthor->getAuOrd(),
                'royaltyPer' => $titleAuthor->getRoyaltyPer(),
            ];
        }, $titleAuthors);

        return $this->render('title_author/index.html.twig', [
            'title_authors' => $titleAuthorsData,
        ]);
    }

    #[Route('/new', name: 'app_title_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $titleAuthor = new TitleAuthor();
        $form = $this->createForm(TitleAuthorType::class, $titleAuthor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($titleAuthor);
            $entityManager->flush();

            return $this->redirectToRoute('app_title_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('title_author/new.html.twig', [
            'title_author' => $titleAuthor,
            'form' => $form,
        ]);
    }

    #[Route('/{author}/{title}', name: 'app_title_author_show', methods: ['GET'])]
    public function show(TitleAuthor $titleAuthor): Response
    {
        return $this->render('title_author/show.html.twig', [
            'title_author' => $titleAuthor,
        ]);
    }

    #[Route('/{author}/{title}/edit', name: 'app_title_author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TitleAuthor $titleAuthor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TitleAuthorType::class, $titleAuthor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_title_author_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('title_author/edit.html.twig', [
            'title_author' => $titleAuthor,
            'form' => $form,
        ]);
    }

    #[Route('/{author}/{title}', name: 'app_title_author_delete', methods: ['POST'])]
    public function delete(Request $request, TitleAuthor $titleAuthor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $titleAuthor->getAuthor()->getAuId() . $titleAuthor->getTitle()->getTitleId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($titleAuthor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_title_author_index', [], Response::HTTP_SEE_OTHER);
    }
}
