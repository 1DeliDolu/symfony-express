<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EmailVerificationController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    #[Route('/verify/email/{token}', name: 'app_verify_email', methods: ['GET'])]
    public function verify(string $token, Request $request): Response
    {
        $repo = $this->entityManager->getRepository(User::class);
        $user = $repo->findOneBy(['verificationToken' => $token]);

        if (null === $user) {
            $this->addFlash('error', 'Geçersiz veya süresi dolmuş doğrulama bağlantısı.');

            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'E-posta adresiniz doğrulandı. Artık giriş yapabilirsiniz.');

        return $this->redirectToRoute('app_login');
    }
}
