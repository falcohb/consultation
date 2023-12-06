<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('locality')
            ->add('postal', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => '+32 475 123 456',
                ],
            ])
            ->add('doctor', TextType::class, [
                'label' => 'Médecin traitant',
                'required' => false,
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'required' => true,
                'years' => range(1930, 2005),
            ])
            ->add('origin', ChoiceType::class, [
                'label' => 'Comment avez-vous découvert EmCare?',
                'choices' => [
                    'Par le bouche à oreille/entourage' => 'Par le bouche à oreille/entourage',
                    'Par mon médecin généraliste' => 'Par mon médecin généraliste',
                    'Par mon psychiatre' => 'Par mon psychiatre',
                    'Par mon psychologue/thérapeute' => 'Par mon psychologue',
                    'Par la presse (magazine/radio)' => 'Par la presse',
                    'Par mes livres' => 'Livre',
                    'Via un annuaire web' => 'annuaire web',
                    'Via un moteur de recherche' => 'moteur de recherche',
                    'Autre' => 'Autre',
                ],
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
