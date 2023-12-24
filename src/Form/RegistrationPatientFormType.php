<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Patient;
use DateInterval;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationPatientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateNow = new \DateTime();
        $majorAge = $dateNow->sub(new DateInterval('P18Y'));
        $majorYear = $majorAge->format('Y');

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => ['class' => 'fw-medium'],
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'label_attr' => ['class' => 'fw-medium'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => ['class' => 'fw-medium'],
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'label_attr' => ['class' => 'fw-medium'],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('locality', TextType::class, [
                'label' => 'Localité',
                'label_attr' => ['class' => 'fw-medium'],
                'required' => true,
            ])
            ->add('postal', TextType::class, [
                'label' => 'Code postal',
                'label_attr' => ['class' => 'fw-medium'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'label_attr' => ['class' => 'fw-medium'],
                'required' => false,
                'attr' => [
                    'placeholder' => '+32 475 123 456',
                ],
            ])
            ->add('doctor', TextType::class, [
                'label' => 'Nom de mon médecin traitant',
                'label_attr' => ['class' => 'fw-medium'],
                'required' => false,
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'label_attr' => ['class' => 'fw-medium'],
                'widget' => 'single_text',
                'required' => true,
                'years' => range($majorYear, 1930),
            ])
            ->add('origin', ChoiceType::class, [
                'label' => 'Comment avez-vous entendu parlé de moi?',
                'label_attr' => ['class' => 'fw-medium'],
                'placeholder' => 'Sélectionnez une option',
                'choices' => [
                    'Par le bouche à oreille/entourage' => 'bouche à oreille',
                    'Par mon médecin généraliste' => 'médecin généraliste',
                    'Par mon psychiatre' => 'psychiatre',
                    'Par mon psychologue/thérapeute' => 'psychologue',
                    'Par les réseaux sociaux' => 'réseaux sociaux',
                    'Par la presse (magazine/radio)' => 'presse',
                    'Par mes livres' => 'livre',
                    'Via un annuaire web' => 'annuaire web',
                    'Via un moteur de recherche' => 'moteur de recherche',
                    'Autre' => 'autre',
                ],
                'autocomplete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
