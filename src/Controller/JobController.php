<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/job')]
#[IsGranted('ROLE_USER')]
final class JobController extends AbstractController
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(name: 'app_job_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $this->jobRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_job_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($job);
            $this->entityManager->flush();
            $this->addFlash('success', 'İş pozisyonu başarıyla oluşturuldu.');

            return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{jobId}', name: 'app_job_show', methods: ['GET'])]
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/{jobId}/edit', name: 'app_job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Job $job): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'İş pozisyonu başarıyla güncellendi.');

            return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{jobId}', name: 'app_job_delete', methods: ['POST'])]
    public function delete(Request $request, Job $job): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getJobId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($job);
            $this->entityManager->flush();
            $this->addFlash('success', 'İş pozisyonu başarıyla silindi.');
        }

        return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
    }
}
