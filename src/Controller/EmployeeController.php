<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee')]
#[IsGranted('ROLE_USER')]
final class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route(name: 'app_employee_index', methods: ['GET'])]
    public function index(): Response
    {
        $employees = $this->employeeRepository->findAll();

        // Serialize employees for Alpine.js
        $employeesData = array_map(function (Employee $employee) {
            $data = [
                'empId' => $employee->getEmpId(),
                'fname' => $employee->getFname() ?? '',
                'minit' => $employee->getMinit(),
                'lname' => $employee->getLname() ?? '',
                'jobLvl' => $employee->getJobLvl() ?? 10,
                'hireDate' => null,
                'job' => null,
                'publisher' => null,
            ];

            // Safely handle hireDate
            if ($employee->getHireDate() !== null) {
                $data['hireDate'] = [
                    'date' => $employee->getHireDate()->format('Y-m-d H:i:s'),
                    'timestamp' => $employee->getHireDate()->getTimestamp(),
                ];
            }

            // Safely handle job
            if ($employee->getJob() !== null) {
                $data['job'] = [
                    'jobId' => $employee->getJob()->getJobId(),
                    'jobDesc' => $employee->getJob()->getJobDesc(),
                ];
            }

            // Safely handle publisher
            if ($employee->getPublisher() !== null) {
                $data['publisher'] = [
                    'pubId' => $employee->getPublisher()->getPubId(),
                    'pubName' => $employee->getPublisher()->getPubName(),
                ];
            }

            return $data;
        }, $employees);

        return $this->render('employee/index.html.twig', [
            'employees' => $employeesData,
        ]);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($employee);
            $this->entityManager->flush();
            $this->addFlash('success', 'Çalışan başarıyla oluşturuldu.');

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{empId}', name: 'app_employee_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['empId' => 'empId'])] Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{empId}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(mapping: ['empId' => 'empId'])] Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Çalışan başarıyla güncellendi.');

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{empId}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(mapping: ['empId' => 'empId'])] Employee $employee): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getEmpId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($employee);
            $this->entityManager->flush();
            $this->addFlash('success', 'Çalışan başarıyla silindi.');
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
