<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Discount;
use App\Form\DiscountType;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/discount')]
final class DiscountController extends AbstractController
{
    #[Route(name: 'app_discount_index', methods: ['GET'])]
    public function index(DiscountRepository $discountRepository): Response
    {
        $discounts = $discountRepository->findAll();

        // Serialize discounts for Alpine.js
        $discountsData = array_map(function (Discount $discount) {
            return [
                'discountType' => $discount->getDiscountType(),
                'lowQty' => $discount->getLowQty(),
                'highQty' => $discount->getHighQty(),
                'discount' => (float)$discount->getDiscount(),
                'store' => $discount->getStore() ? [
                    'storId' => $discount->getStore()->getStorId(),
                    'storName' => $discount->getStore()->getStorName(),
                ] : null,
            ];
        }, $discounts);

        return $this->render('discount/index.html.twig', [
            'discounts' => $discountsData,
        ]);
    }

    #[Route('/new', name: 'app_discount_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $discount = new Discount();
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($discount);
            $entityManager->flush();

            return $this->redirectToRoute('app_discount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discount/new.html.twig', [
            'discount' => $discount,
            'form' => $form,
        ]);
    }

    #[Route('/{discountType}', name: 'app_discount_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['discountType' => 'discountType'])] Discount $discount): Response
    {
        return $this->render('discount/show.html.twig', [
            'discount' => $discount,
        ]);
    }

    #[Route('/{discountType}/edit', name: 'app_discount_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(mapping: ['discountType' => 'discountType'])] Discount $discount, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_discount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discount/edit.html.twig', [
            'discount' => $discount,
            'form' => $form,
        ]);
    }

    #[Route('/{discountType}', name: 'app_discount_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(mapping: ['discountType' => 'discountType'])] Discount $discount, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $discount->getDiscountType(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($discount);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_discount_index', [], Response::HTTP_SEE_OTHER);
    }
}
