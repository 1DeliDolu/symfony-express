<?php

namespace App\Controller\Northwind;

use App\Entity\Northwind\Employees;
use App\Form\Northwind\EmployeesType;
use App\Repository\Northwind\EmployeesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/northwind/employees')]
final class EmployeesController extends AbstractController
{
    #[Route(name: 'app_northwind_employees', methods: ['GET'])]
    public function index(EmployeesRepository $employeesRepository): Response
    {
        return $this->render('northwind/employees/index.html.twig', [
            'employees' => $employeesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_northwind_employees_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employees();
        $form = $this->createForm(EmployeesType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_employees', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/employees/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_northwind_employees_show', methods: ['GET'])]
    public function show(Employees $employee): Response
    {
        return $this->render('northwind/employees/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_northwind_employees_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employees $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeesType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_northwind_employees', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('northwind/employees/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_northwind_employees_delete', methods: ['POST'])]
    public function delete(Request $request, Employees $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_northwind_employees', [], Response::HTTP_SEE_OTHER);
    }
}
