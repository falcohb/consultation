<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Patient;
use App\Form\RegistrationPatientFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationPatientController extends AbstractController
{
    public function __construct(private readonly EmailVerifier $emailVerifier)
    {
    }

    #[Route('/patient/register', name: 'app_patient_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $patientPasswordHasher,
        EntityManagerInterface $entityManager,
    ): ?Response {
        $patient = new Patient();
        $form = $this->createForm(RegistrationPatientFormType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (is_string($plainPassword)) {
                $patient->setPassword(
                    $patientPasswordHasher->hashPassword(
                        $patient,
                        $plainPassword
                    )
                );
            }

            $entityManager->persist($patient);
            $entityManager->flush();

            // generate a signed url and email it to the user
            if ($patient->getEmail()) {
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $patient,
                    (new TemplatedEmail())
                        ->from(new Address('verify@cathyassenheim.com', 'Cathy Assenheim'))
                        ->to($patient->getEmail())
                        ->subject('Confirmation de votre adresse mail')
                        ->htmlTemplate('emails/confirmation_email.html.twig')
                );
            }

            $this->addFlash('info', 'Pour activer votre compte, merci de cliquer sur le lien que 
            nous vous avons envoyé par e-mail dans un délai d\'une heure.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.patient.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        VerifyEmailHelperInterface $verifyEmailHelper,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            $userEmail = $user->getEmail();
            if ($userEmail !== null) {
                $verifyEmailHelper->validateEmailConfirmation(
                    $request->getUri(),
                    (string) $user->getId(),
                    $userEmail
                );
            }
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est vérifié! Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/verify/resend', name: 'app_verify_resend_email')]
    public function resendVerifyEmail(
        Request $request,
        UserRepository $userRepository,
    ): Response {
        $email = $request->request->get('resend_email');

        if ($email) {
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            if ($user->getIsVerified() === false) {
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('verify@cathyassenheim.com', 'Cathy Assenheim'))
                        ->to((string) $email)
                        ->subject('Confirmation de votre adresse mail')
                        ->htmlTemplate('emails/confirmation_email.html.twig')
                );

                $this->addFlash('info', 'Pour activer votre compte, merci de cliquer sur le lien que nous 
                vous avons envoyé par e-mail dans un délai d\'une heure.');

                return $this->redirectToRoute('app_verify_resend_email');
            }
        }

        return $this->render('registration/resend_verify_email.html.twig');
    }
}
