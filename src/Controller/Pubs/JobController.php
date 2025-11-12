<?php

declare(strict_types=1);

namespace App\Controller\Pubs;

use App\Entity\Pubs\Job;
use App\Form\Pubs\JobType;
use App\Repository\Pubs\JobRepository;
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
    ) {}

    #[Route(name: 'app_job_index', methods: ['GET'])]
    public function index(): Response
    {
        $jobs = $this->jobRepository->findAll();

        // Serialize jobs for Alpine.js
        $jobsData = array_map(function (Job $job) {
            return [
                'jobId' => $job->getJobId(),
                'jobDesc' => $job->getJobDesc(),
                'minLvl' => $job->getMinLvl(),
                'maxLvl' => $job->getMaxLvl(),
            ];
        }, $jobs);

        return $this->render('pubs/job/index.html.twig', [
            'jobs' => $jobsData,
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

        return $this->render('pubs/job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{jobId}', name: 'app_job_show', methods: ['GET'])]
    public function show(int $jobId): Response
    {
        $job = $this->jobRepository->find($jobId);

        if (!$job) {
            throw $this->createNotFoundException('Die Position wurde nicht gefunden.');
        }

        return $this->render('pubs/job/show.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/{jobId}/edit', name: 'app_job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $jobId): Response
    {
        $job = $this->jobRepository->find($jobId);

        if (!$job) {
            throw $this->createNotFoundException('Die Position wurde nicht gefunden.');
        }

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'İş pozisyonu başarıyla güncellendi.');

            return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pubs/job/edit.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{jobId}', name: 'app_job_delete', methods: ['POST'])]
    public function delete(Request $request, int $jobId): Response
    {
        $job = $this->jobRepository->find($jobId);

        if (!$job) {
            throw $this->createNotFoundException('Die Position wurde nicht gefunden.');
        }

        if ($this->isCsrfTokenValid('delete' . $job->getJobId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($job);
            $this->entityManager->flush();
            $this->addFlash('success', 'İş pozisyonu başarıyla silindi.');
        }

        return $this->redirectToRoute('app_job_index', [], Response::HTTP_SEE_OTHER);
    }
}

