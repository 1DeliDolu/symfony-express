<?php

namespace App\Controller\Northwind;

use App\Entity\Northwind\Shippers;
use App\Form\Northwind\ShippersType;
use App\Repository\Northwind\ShippersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/northwind/shippers')]
final class ShippersController extends AbstractController
{
    #[Route(name: 'app_northwind_shippers_index', methods: ['GET'])]
    public function index(ShippersRepository $shippersRepository): Response
    {
        return $this->render('northwind/shippers/index.html.twig', [
            'shippers' => $shippersRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_northwind_shippers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shipper = new Shippers();
        $form = $this->createForm(ShippersType::class, $shipper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shipper);
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_shippers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/shippers/new.html.twig', [
            'shipper' => $shipper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_northwind_shippers_show', methods: ['GET'])]
    public function show(Shippers $shipper): Response
    {
        return $this->render('northwind/shippers/show.html.twig', [
            'shipper' => $shipper,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_northwind_shippers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shippers $shipper, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShippersType::class, $shipper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_shippers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/shippers/edit.html.twig', [
            'shipper' => $shipper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_northwind_shippers_delete', methods: ['POST'])]
    public function delete(Request $request, Shippers $shipper, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shipper->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shipper);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_northwind_shippers_index', [], Response::HTTP_SEE_OTHER);
    }
}
