<?php

namespace App\Controller\Northwind;

use App\Entity\Northwind\Territories;
use App\Form\Northwind\TerritoriesType;
use App\Repository\Northwind\TerritoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/northwind/territories')]
final class TerritoriesController extends AbstractController
{
    #[Route(name: 'app_northwind_territories_index', methods: ['GET'])]
    public function index(TerritoriesRepository $territoriesRepository): Response
    {
        return $this->render('northwind/territories/index.html.twig', [
            'territories' => $territoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_northwind_territories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $territory = new Territories();
        $form = $this->createForm(TerritoriesType::class, $territory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($territory);
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_territories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/territories/new.html.twig', [
            'territory' => $territory,
            'form' => $form,
        ]);
    }

    #[Route('/{territoryId}', name: 'app_northwind_territories_show', methods: ['GET'])]
    public function show(Territories $territory): Response
    {
        return $this->render('northwind/territories/show.html.twig', [
            'territory' => $territory,
        ]);
    }

    #[Route('/{territoryId}/edit', name: 'app_northwind_territories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Territories $territory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TerritoriesType::class, $territory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_territories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/territories/edit.html.twig', [
            'territory' => $territory,
            'form' => $form,
        ]);
    }

    #[Route('/{territoryId}', name: 'app_northwind_territories_delete', methods: ['POST'])]
    public function delete(Request $request, Territories $territory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$territory->getTerritoryId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($territory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_northwind_territories_index', [], Response::HTTP_SEE_OTHER);
    }
}
