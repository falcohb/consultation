<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Patient;
use App\Form\RegistrationPatientFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationPatientController extends AbstractController
{
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

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.patient.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
