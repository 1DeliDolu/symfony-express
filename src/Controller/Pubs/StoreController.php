<?php

declare(strict_types=1);

namespace App\Controller\Pubs;

use App\Entity\Pubs\Store;
use App\Form\Pubs\StoreType;
use App\Repository\Pubs\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/store')]
#[IsGranted('ROLE_USER')]
final class StoreController extends AbstractController
{
    public function __construct(
        private readonly StoreRepository $storeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(name: 'app_store_index', methods: ['GET'])]
    public function index(): Response
    {
        $stores = $this->storeRepository->findAll();

        // Serialize stores for Alpine.js
        $storesData = array_map(function (Store $store) {
            return [
                'storId' => $store->getStorId(),
                'storName' => $store->getStorName() ?? 'N/A',
                'storAddress' => $store->getStorAddress() ?? 'N/A',
                'city' => $store->getCity() ?? 'N/A',
                'state' => $store->getState() ?? 'N/A',
                'zip' => $store->getZip() ?? 'N/A',
            ];
        }, $stores);

        return $this->render('pubs/store/index.html.twig', [
            'stores' => $storesData,
        ]);
    }

    #[Route('/new', name: 'app_store_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $store = new Store();
        $form = $this->createForm(StoreType::class, $store, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($store);
            $this->entityManager->flush();
            $this->addFlash('success', 'Mağaza başarıyla oluşturuldu.');

            return $this->redirectToRoute('app_store_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pubs/store/new.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/{storId}', name: 'app_store_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['storId' => 'storId'])] Store $store): Response
    {
        return $this->render('pubs/store/show.html.twig', [
            'store' => $store,
        ]);
    }

    #[Route('/{storId}/edit', name: 'app_store_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(mapping: ['storId' => 'storId'])] Store $store): Response
    {
        $form = $this->createForm(StoreType::class, $store, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Mağaza başarıyla güncellendi.');

            return $this->redirectToRoute('app_store_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pubs/store/edit.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/{storId}', name: 'app_store_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(mapping: ['storId' => 'storId'])] Store $store): Response
    {
        if ($this->isCsrfTokenValid('delete' . $store->getStorId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($store);
            $this->entityManager->flush();
            $this->addFlash('success', 'Mağaza başarıyla silindi.');
        }

        return $this->redirectToRoute('app_store_index', [], Response::HTTP_SEE_OTHER);
    }
}

