<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Schedule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('schedule', EntityType::class, [
                'label' => false,
                'placeholder' => 'Sélectionnez une date',
                'class' => Schedule::class,
                'choice_label' => 'formattedDate',
                'choices' => $this->getAvailableDates(),
                'autocomplete' => true,
            ])
            ->add('isVirtual', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Sélectionnez un type de rendez-vous',
                'choices' => [
                    'A distance (visioconférence)' => 1,
                    'En présentiel' => 0,
                ],
                'autocomplete' => true,
            ])
            ->add('subject', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Sélectionnez l\'objet de la consultation',
                'choices' => [
                    'Bilan hypersensibilité' => 'hyper',
                    'Burnout' => 'burnout',
                    'Autre' => 'autre',
                ],
                'autocomplete' => true,
            ])
            ->add('isAdult', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Pour qui prenez-vous rendez-vous?',
                'choices' => [
                    'Pour moi (adulte)' => 1,
                    'Pour un enfant ou un adolescent' => 0,
                ],
                'autocomplete' => true,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Avez-vous des informations importantes à me communiquer sur votre état de santé?',
                'attr' => [
                    'rows' => 5,
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }

    /**
     * @return array<Schedule> Returns an array of Schedule objects
     */
    public function getAvailableDates(): array
    {
        $repository = $this->entityManager->getRepository(Schedule::class);
        return $repository->findAllAvailableDate();
    }
}
