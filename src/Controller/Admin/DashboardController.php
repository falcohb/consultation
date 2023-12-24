<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Patient;
use App\Entity\Schedule;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $dashboardItems = [];

        $dashboardItems[] = [
            'label' => 'Administrateurs',
            'color' => User::COLOR,
            'icon' => User::ICON,
            'items' => $this->entityManager->getRepository(User::class)->countUsersAdminByRole(),
            'link' => $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl(),
            'access' => 'ROLE_ADMIN',
        ];

        $dashboardItems[] = [
            'label' => 'Patients',
            'color' => Patient::COLOR,
            'icon' => Patient::ICON,
            'items' => $this->entityManager->getRepository(User::class)->countUsersByRole(),
            'link' => $this->adminUrlGenerator->setController(PatientCrudController::class)->generateUrl(),
            'access' => 'ROLE_ADMIN',
        ];

        return $this->render('admin/dashboard.html.twig', ['dashboardItems' => $dashboardItems]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Diagnostic Admin');
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->setPageTitle('index', 'Tableau de bord');
    }

    /**
     * @return array<MenuItemInterface>
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-gear');
        yield MenuItem::section('Rendez-vous');
        yield MenuItem::linkToCrud('Date & horaire', Schedule::ICON, Schedule::class);
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Patients', Patient::ICON, Patient::class);
        yield MenuItem::linkToCrud('Administrateurs', User::ICON, User::class);
        yield MenuItem::section();
        yield MenuItem::linkToUrl('Retour sur le site', 'fas fa-reply', $this->generateUrl('app_home'));
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('build/admin.css');
    }
}
