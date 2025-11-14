<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
    ) {}

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, RateLimiterFactory $registrationLimiter): Response
    {
        // Redirect if already authenticated
        if ($this->getUser()) {
            return $this->redirectToRoute('app_store_index');
        }

        // Apply rate limiting
        $limiter = $registrationLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            $this->addFlash('error', 'Çok fazla kayıt denemesi yaptınız. Lütfen daha sonra tekrar deneyin.');

            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (null === $plainPassword) {
                $this->addFlash('error', 'Parola alanı boş olamaz.');

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // generate verification token
            $token = bin2hex(random_bytes(32));
            $user->setVerificationToken($token);
            $user->setIsVerified(false);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // send verification email
            $verifyUrl = $this->generateUrl('app_verify_email', ['token' => $token], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@localhost', 'No Reply'))
                ->to(new Address($user->getEmail()))
                ->subject('Hesabınızı doğrulayın')
                ->htmlTemplate('emails/verify_email.html.twig')
                ->context([
                    'user' => $user,
                    'verifyUrl' => $verifyUrl,
                ]);

            try {
                $this->mailer->send($email);
            } catch (\Throwable $e) {
                // Log or ignore - still create the account
            }

            $this->addFlash('success', 'Hesabınız oluşturuldu. Lütfen e-posta adresinizi doğrulamak için posta kutunuzu kontrol edin.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
