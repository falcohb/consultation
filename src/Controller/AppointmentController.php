<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Patient;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Service\CalendarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rendezvous')]
#[IsGranted('ROLE_USER')]
class AppointmentController extends AbstractController
{
    public function __construct(
        private readonly AppointmentRepository $appointmentRepository,
        private readonly CalendarService $calendarService,
    ) {
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Patient $patient): Response
    {
        $patientId = $patient->getId();

        if ($patientId !== null) {
            $appointment = $this->appointmentRepository->getUpcomingAppointmentByPatient($patientId);
            $pastAppointments = $this->appointmentRepository->getPastAppointmentsByPatient($patientId);

            $this->denyAccessUnlessGranted('VIEW_APPOINTMENT', $appointment);

            return $this->render('appointment/show.html.twig', [
                'appointment' => $appointment,
                'pastAppointments' => $pastAppointments,
            ]);
        }
        throw new \InvalidArgumentException('Patient cannot be null.');
    }

    #[Route('/', name: 'app_appointment_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Patient $patient */
            $patient = $this->getUser();
            $appointment->setPatient($patient);

            $entityManager->persist($appointment);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_appointment_show',
                ['id' => $patient->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_delete', methods: ['POST'])]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $patient = $appointment->getPatient();

        if (!$patient) {
            throw new NotFoundHttpException('Patient not found.');
        }

        if (
            $this->isCsrfTokenValid(
                'delete' . $appointment->getId(),
                (string) $request->request->get('_token')
            )
        ) {
            // Resets the appointment to available
            $schedule = $appointment->getSchedule();

            // Check whether the appointment has a valid timetable
            if ($schedule) {
                $schedule->setIsAvailable(true);
                $entityManager->persist($schedule);
            } else {
                throw new NotFoundHttpException('Appointment not found');
            }

            // Delete appointment
            $entityManager->remove($appointment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appointment_show', ['id' => $patient->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/icalendar', name: 'app_appointment_icalendar', methods: ['GET'])]
    public function getIcalendar(Patient $patient): Response
    {
        $patientId = $patient->getId();

        if ($patientId !== null) {
            $appointment = $this->appointmentRepository->getUpcomingAppointmentByPatient($patientId);

            $location = '';
            if ($appointment !== null) {
                $schedule = $appointment->getSchedule();
                if ($schedule !== null && $schedule->isIsVirtual()) {
                    $location = 'En visioconférence';
                } else {
                    $location = 'Clé des Champs 4 - 1380 Ohain';
                }
            }

            $eventName = 'Rendez-vous avec Cathy Assenheim';
            $eventDescription = 'Consultation au cabinet de neuropsychologie de Cathy Assenheim';

            $startDate = null;
            if ($appointment !== null) {
                $schedule = $appointment->getSchedule();
                $startDate = $schedule?->getDate();
            }

            $endDate = $startDate !== null ? clone $startDate : null;
            $endDate?->modify('+1 hour');

            $icsContent = $this->calendarService->generateICalendarLink(
                $eventName,
                $eventDescription,
                $startDate !== null ? $startDate : new \DateTime(),
                $endDate !== null ? $endDate : new \DateTime(),
                $location,
                'https://consultations.cathyassenheim.com'
            );

            $response = new Response($icsContent);
            $response->headers->set('Content-type', 'text/calendar; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename=event.ics');

            return $response;
        }
        throw new \InvalidArgumentException('Patient cannot be null.');
    }
}
