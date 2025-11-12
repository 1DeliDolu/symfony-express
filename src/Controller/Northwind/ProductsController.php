<?php

namespace App\Controller\Northwind;

use App\Entity\Northwind\Products;
use App\Form\Northwind\ProductsType;
use App\Repository\Northwind\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/northwind/products')]
final class ProductsController extends AbstractController
{
    #[Route(name: 'app_northwind_products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('northwind/products/index.html.twig', [
            'products' => $productsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_northwind_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{productId}', name: 'app_northwind_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('northwind/products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{productId}/edit', name: 'app_northwind_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{productId}', name: 'app_northwind_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getProductId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_northwind_products_index', [], Response::HTTP_SEE_OTHER);
    }
}
