<?php

namespace App\Controller;

use App\Entity\Northwind\Employees;
use App\Repository\Northwind\EmployeesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/northwind')]
class NorthwindEmployeesController extends AbstractController
{
    public function __construct(
        private readonly EmployeesRepository $employeesRepository
    ) {}

    #[Route('/employees', name: 'app_northwind_employees', methods: ['GET'])]
    public function index(): Response
    {
        $employees = $this->employeesRepository->findAll();

        return $this->render('northwind/employees/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employees/{id}', name: 'app_northwind_employees_show', methods: ['GET'])]
    public function show(Employees $employee): Response
    {
        return $this->render('northwind/employees/show.html.twig', [
            'employee' => $employee,
        ]);
    }
}
