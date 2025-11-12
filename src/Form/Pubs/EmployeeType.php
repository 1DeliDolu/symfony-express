<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Employee;
use App\Entity\Pubs\Job;
use App\Entity\Pubs\Publisher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'] ?? true;

        $builder
            ->add('empId', TextType::class, [
                'label' => 'Mitarbeiter-ID',
                'help' => 'Format: XXX-XXXXX oder 9 Ziffern',
                'attr' => [
                    'placeholder' => 'ABC-M1234',
                    'maxlength' => 9,
                ],
                'disabled' => !$isNew,
            ])
            ->add('fname', TextType::class, [
                'label' => 'Vorname',
                'attr' => [
                    'placeholder' => 'Vorname',
                    'maxlength' => 20,
                ],
            ])
            ->add('minit', TextType::class, [
                'label' => 'Zweiter Vorname',
                'required' => false,
                'attr' => [
                    'placeholder' => 'M',
                    'maxlength' => 1,
                ],
            ])
            ->add('lname', TextType::class, [
                'label' => 'Nachname',
                'attr' => [
                    'placeholder' => 'Nachname',
                    'maxlength' => 30,
                ],
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'jobDesc',
                'label' => 'Berufsbezeichnung',
                'placeholder' => 'Wählen Sie eine Position',
            ])
            ->add('jobLvl', IntegerType::class, [
                'label' => 'Berufsebene',
                'help' => 'Muss zwischen 10 und 250 liegen',
                'required' => false,
                'attr' => [
                    'min' => 10,
                    'max' => 250,
                ],
            ])
            ->add('publisher', EntityType::class, [
                'class' => Publisher::class,
                'choice_label' => fn (Publisher $pub) => $pub->getPubName().' ('.$pub->getPubId().')',
                'label' => 'Verlag',
                'placeholder' => 'Wählen Sie einen Verlag',
            ])
            ->add('hireDate', DateTimeType::class, [
                'label' => 'Einstellungsdatum',
                'widget' => 'single_text',
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
            'is_new' => true,
        ]);
    }
}
