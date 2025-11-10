<?php

namespace App\Controller;

use App\Entity\Roysched;
use App\Form\RoyschedType;
use App\Repository\RoyschedRepository;
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
        return $this->render('roysched/index.html.twig', [
            'royscheds' => $royschedRepository->findAll(),
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

        return $this->render('roysched/new.html.twig', [
            'roysched' => $roysched,
            'form' => $form,
        ]);
    }

    #[Route('/{title}', name: 'app_roysched_show', methods: ['GET'])]
    public function show(Roysched $roysched): Response
    {
        return $this->render('roysched/show.html.twig', [
            'roysched' => $roysched,
        ]);
    }

    #[Route('/{title}/edit', name: 'app_roysched_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Roysched $roysched, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoyschedType::class, $roysched);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_roysched_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('roysched/edit.html.twig', [
            'roysched' => $roysched,
            'form' => $form,
        ]);
    }

    #[Route('/{title}', name: 'app_roysched_delete', methods: ['POST'])]
    public function delete(Request $request, Roysched $roysched, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$roysched->getTitle(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($roysched);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_roysched_index', [], Response::HTTP_SEE_OTHER);
    }
}
