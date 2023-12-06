<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Patient;
use App\Service\UserCsvService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

class PatientCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserCsvService $userCsvService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Patient::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Patients')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un patient')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un patient')
            ->setPageTitle(Crud::PAGE_DETAIL, static function (Patient $patient) {
                return $patient->getDisplayName();
            })
            ->setEntityPermission('ROLE_ADMIN')
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('exportUsers', 'Export', 'fa fa-download')
            ->displayAsLink()
            ->createAsGlobalAction()
            ->linkToCrudAction('export')
        ;

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $exportAction);
    }

    /**
     * @return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();
        $email = EmailField::new('email');
        $displayName = TextField::new('displayName', 'Nom')->onlyOnIndex();
        $firstName = TextField::new('firstName', 'Prénom')->onlyOnForms();
        $lastName = TextField::new('lastName', 'Nom')->onlyOnForms();
        $locality = TextField::new('locality', 'Localité');
        $postal = TextField::new('postal', 'Code postal');
        $phone = TextField::new('phone', 'Téléphone');
        $doctor = TextField::new('doctor', 'Médecin traitant')->hideOnIndex();
        $lastLoginAt = DateTimeField::new('lastLoginAt', 'Dernière connexion')
            ->setFormat('long', 'short');
        $createdAt = DateTimeField::new('createdAt', 'Inscrit le')
            ->setFormat('long', 'short');
        $isActive = BooleanField::new('isActive', 'Est actif?')->hideOnIndex();
        $isVerified = BooleanField::new('isVerified', 'Email vérifié?');
        $origin = TextField::new('origin', 'Connu par');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $displayName, $email, $locality, $createdAt, $isVerified, $lastLoginAt ];
        }

        return [
            $firstName,
            $lastName,
            $email,
            $locality,
            $postal,
            $phone,
            $doctor,
            $origin,
            $isActive,
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
