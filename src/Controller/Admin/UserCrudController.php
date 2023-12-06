<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\UserCsvService;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserCsvService $userCsvService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.roles LIKE :value')
            ->setParameter('value', '%"ROLE_ADMIN"%');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Administrateurs')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un administrateur')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un administrateur')
            ->setEntityPermission('ROLE_ADMIN')
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('exportUsers', 'Export', 'fa fa-download')
            ->displayAsLink()
            ->createAsGlobalAction()
            ->linkToCrudAction('export');

        return parent::configureActions($actions)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action) {
                $action->displayIf(static function (User $user) {
                    return !$user->getIsVerified();
                });

                return $action;
            });
    }

    /**
     * @return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $roles = ArrayField::new('Roles')->onlyOnForms();
        $email = EmailField::new('email');
        $displayName = TextField::new('displayName', 'Nom');
        $firstName = TextField::new('firstName', 'Prénom');
        $lastName = TextField::new('lastName', 'Nom');
        $lastLoginAt = DateTimeField::new('lastLoginAt', 'Dernière connexion')
            ->setFormat('long', 'short');
        $createdAt = DateTimeField::new('createdAt', 'Inscrit le')
            ->setFormat('long', 'short');
        $isVerified = BooleanField::new('isVerified', 'Vérifié?')
            ->renderAsSwitch(false);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $displayName, $email, $roles, $lastLoginAt, $createdAt, $isVerified ];
        }

        return [
            $firstName,
            $lastName,
            $email,
            $roles,
            $isVerified,
        ];
    }

    public function export(): Response
    {
        return $this->userCsvService->exportCsv();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add('isVerified');
    }
}
