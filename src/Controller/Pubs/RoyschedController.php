<?php

declare(strict_types=1);

namespace App\Controller\Pubs;

use App\Entity\Pubs\Roysched;
use App\Form\Pubs\RoyschedType;
use App\Repository\Pubs\RoyschedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/roysched')]
final class RoyschedController extends AbstractController
{
    #[Route(name: 'app_roysched_index', methods: ['GET'])]
    public function index(RoyschedRepository $royschedRepository): Response
    {
        $royscheds = $royschedRepository->findAll();

        // Prepare data for JSON serialization
        $royschedsData = array_map(function ($roysched) {
            return [
                'title' => [
                    'titleId' => $roysched->getTitle()->getTitleId(),
                    'title' => $roysched->getTitle()->getTitle(),
                ],
                'lorange' => $roysched->getLorange(),
                'hirange' => $roysched->getHirange(),
                'royalty' => $roysched->getRoyalty(),
            ];
        }, $royscheds);

        return $this->render('pubs/roysched/index.html.twig', [
            'royscheds' => $royschedsData,
        ]);
    }

    #[Route('/new', name: 'app_roysched_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $roysched = new Roysched();
        $form = $this->createForm(RoyschedType::class, $roysched);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($roysched);
            $entityManager->flush();

            return $this->redirectToRoute('app_roysched_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pubs/roysched/new.html.twig', [
            'roysched' => $roysched,
            'form' => $form,
        ]);
    }

    #[Route('/{title}', name: 'app_roysched_show', methods: ['GET'])]
    public function show(string $title, RoyschedRepository $royschedRepository): Response
    {
        $roysched = $royschedRepository->findOneByTitleId($title);

        if (!$roysched) {
            throw $this->createNotFoundException('Der Tantiemenplan wurde nicht gefunden.');
        }

        return $this->render('pubs/roysched/show.html.twig', [
            'roysched' => $roysched,
        ]);
    }

    #[Route('/{title}/edit', name: 'app_roysched_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $title, RoyschedRepository $royschedRepository, EntityManagerInterface $entityManager): Response
    {
        $roysched = $royschedRepository->findOneByTitleId($title);

        if (!$roysched) {
            throw $this->createNotFoundException('Der Tantiemenplan wurde nicht gefunden.');
        }

        $form = $this->createForm(RoyschedType::class, $roysched);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_roysched_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pubs/roysched/edit.html.twig', [
            'roysched' => $roysched,
            'form' => $form,
        ]);
    }

    #[Route('/{title}', name: 'app_roysched_delete', methods: ['POST'])]
    public function delete(Request $request, string $title, RoyschedRepository $royschedRepository, EntityManagerInterface $entityManager): Response
    {
        $roysched = $royschedRepository->findOneByTitleId($title);

        if (!$roysched) {
            throw $this->createNotFoundException('Der Tantiemenplan wurde nicht gefunden.');
        }

        if ($this->isCsrfTokenValid('delete' . $roysched->getTitle()->getTitleId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($roysched);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_roysched_index', [], Response::HTTP_SEE_OTHER);
    }
}

