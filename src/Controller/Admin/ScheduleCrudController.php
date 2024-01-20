<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Schedule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class ScheduleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Schedule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Dates de rendez-vous')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier une date')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une date')
            ->setEntityPermission('ROLE_ADMIN')
            ->setDefaultSort(['date' => 'desc'])
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-plus')->setLabel('Ajouter une date');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateTimeField::new('date', 'Date'),
            BooleanField::new('isAvailable', 'Date disponible?'),
        ];
    }
}
