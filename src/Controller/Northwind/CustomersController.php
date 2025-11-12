<?php

namespace App\Controller\Northwind;

use App\Entity\Northwind\Customers;
use App\Form\Northwind\CustomersType;
use App\Repository\Northwind\CustomersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/northwind/customers')]
final class CustomersController extends AbstractController
{
    #[Route(name: 'app_northwind_customers_index', methods: ['GET'])]
    public function index(CustomersRepository $customersRepository): Response
    {
        return $this->render('northwind/customers/index.html.twig', [
            'customers' => $customersRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_northwind_customers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $customer = new Customers();
        $form = $this->createForm(CustomersType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager('northwind');
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('app_northwind_customers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/customers/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{customerId}', name: 'app_northwind_customers_show', methods: ['GET'])]
    public function show(Customers $customer): Response
    {
        return $this->render('northwind/customers/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{customerId}/edit', name: 'app_northwind_customers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customers $customer, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CustomersType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager('northwind');
            $em->flush();

            return $this->redirectToRoute('app_northwind_customers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/customers/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{customerId}', name: 'app_northwind_customers_delete', methods: ['POST'])]
    public function delete(Request $request, Customers $customer, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getCustomerId(), $request->getPayload()->getString('_token'))) {
            $em = $doctrine->getManager('northwind');
            $em->remove($customer);
            $em->flush();
        }

        return $this->redirectToRoute('app_northwind_customers_index', [], Response::HTTP_SEE_OTHER);
    }
}
