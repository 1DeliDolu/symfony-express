<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Title;
use App\Form\TitleType;
use App\Repository\TitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/title')]
#[IsGranted('ROLE_USER')]
final class TitleController extends AbstractController
{
    public function __construct(
        private readonly TitleRepository $titleRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'app_title_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('title/index.html.twig', [
            'titles' => $this->titleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_title_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $title = new Title();
        $form = $this->createForm(TitleType::class, $title);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($title);
            $this->entityManager->flush();

            $this->addFlash('success', 'Title created successfully.');

            return $this->redirectToRoute('app_title_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('title/new.html.twig', [
            'title' => $title,
            'form' => $form,
        ]);
    }

    #[Route('/{titleId}', name: 'app_title_show', methods: ['GET'])]
    public function show(Title $title): Response
    {
        return $this->render('title/show.html.twig', [
            'title' => $title,
        ]);
    }

    #[Route('/{titleId}/edit', name: 'app_title_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Title $title): Response
    {
        $form = $this->createForm(TitleType::class, $title);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Title updated successfully.');

            return $this->redirectToRoute('app_title_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('title/edit.html.twig', [
            'title' => $title,
            'form' => $form,
        ]);
    }

    #[Route('/{titleId}', name: 'app_title_delete', methods: ['POST'])]
    public function delete(Request $request, Title $title): Response
    {
        if ($this->isCsrfTokenValid('delete'.$title->getTitleId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($title);
            $this->entityManager->flush();

            $this->addFlash('success', 'Title deleted successfully.');
        }

        return $this->redirectToRoute('app_title_index', [], Response::HTTP_SEE_OTHER);
    }
}
