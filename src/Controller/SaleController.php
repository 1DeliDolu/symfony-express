<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sale;
use App\Form\SaleType;
use App\Repository\SaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sale')]
final class SaleController extends AbstractController
{
    #[Route(name: 'app_sale_index', methods: ['GET'])]
    public function index(SaleRepository $saleRepository): Response
    {
        $sales = $saleRepository->findAll();

        // Serialize sales for Alpine.js
        $salesData = array_map(function (Sale $sale) {
            $data = [
                'storId' => $sale->getStore()->getStorId(),
                'storName' => $sale->getStore()->getStorName() ?? 'N/A',
                'ordNum' => $sale->getOrdNum(),
                'titleId' => $sale->getTitle()->getTitleId(),
                'titleName' => $sale->getTitle()->getTitle() ?? 'N/A',
                'ordDate' => [
                    'date' => $sale->getOrdDate()->format('Y-m-d H:i:s'),
                    'timestamp' => $sale->getOrdDate()->getTimestamp(),
                ],
                'qty' => $sale->getQty(),
                'payterms' => $sale->getPayterms(),
            ];

            return $data;
        }, $sales);

        return $this->render('sale/index.html.twig', [
            'sales' => $salesData,
        ]);
    }

    #[Route('/new', name: 'app_sale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sale = new Sale();
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sale);
            $entityManager->flush();

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sale/new.html.twig', [
            'sale' => $sale,
            'form' => $form,
        ]);
    }

    #[Route('/{store}/{ordNum}/{title}', name: 'app_sale_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['store' => 'store', 'ordNum' => 'ordNum', 'title' => 'title'])]
        Sale $sale
    ): Response {
        return $this->render('sale/show.html.twig', [
            'sale' => $sale,
        ]);
    }

    #[Route('/{store}/{ordNum}/{title}/edit', name: 'app_sale_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['store' => 'store', 'ordNum' => 'ordNum', 'title' => 'title'])]
        Sale $sale,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sale/edit.html.twig', [
            'sale' => $sale,
            'form' => $form,
        ]);
    }

    #[Route('/{store}/{ordNum}/{title}', name: 'app_sale_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['store' => 'store', 'ordNum' => 'ordNum', 'title' => 'title'])]
        Sale $sale,
        EntityManagerInterface $entityManager
    ): Response {
        $tokenId = 'delete' . $sale->getStore()->getStorId() . $sale->getOrdNum() . $sale->getTitle()->getTitleId();
        if ($this->isCsrfTokenValid($tokenId, $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
    }
}
