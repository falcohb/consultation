<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ScheduleRepository $scheduleRepository): Response
    {
        $schedule = $scheduleRepository->findAllAvailableDate();

        if ($schedule) {
            $dates = [];
            foreach ($schedule as $date) {
                $dates[] = $date->getDate();
            }

            $firstDate = min($dates);
        } else {
            $firstDate = null;
        }

        return $this->render('home/index.html.twig', [
            'firstDate' => $firstDate,
        ]);
    }
}
