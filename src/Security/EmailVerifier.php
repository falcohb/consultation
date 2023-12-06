<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier extends AbstractController
{
    public function __construct(
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function sendEmailConfirmation(
        string $verifyEmailRouteName,
        UserInterface $user,
        TemplatedEmail $email
    ): void {
        $user = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if ($user !== null) {
            if ($user->getEmail() && $user->getId()) {
                $signatureComponents = $this->verifyEmailHelper->generateSignature(
                    $verifyEmailRouteName,
                    (string) $user->getId(),
                    $user->getEmail(),
                    ['id' => $user->getId()]
                );

                $context = $email->getContext();
                $context['signedUrl'] = $signatureComponents->getSignedUrl();
                $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
                $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

                $email->context($context);

                $this->mailer->send($email);
            }
        }
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $user = $this->userRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        if ($user->getEmail()) {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                (string) $user->getId(),
                $user->getEmail()
            );

            $user->setIsVerified(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
