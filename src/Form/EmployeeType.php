<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Job;
use App\Entity\Publisher;
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
                'label' => 'Çalışan ID',
                'help' => 'Format: XXX-XXXXX veya 9 rakam',
                'attr' => [
                    'placeholder' => 'ABC-M1234',
                    'maxlength' => 9,
                ],
                'disabled' => !$isNew,
            ])
            ->add('fname', TextType::class, [
                'label' => 'Ad',
                'attr' => [
                    'placeholder' => 'Ad',
                    'maxlength' => 20,
                ],
            ])
            ->add('minit', TextType::class, [
                'label' => 'Orta İsim',
                'required' => false,
                'attr' => [
                    'placeholder' => 'M',
                    'maxlength' => 1,
                ],
            ])
            ->add('lname', TextType::class, [
                'label' => 'Soyad',
                'attr' => [
                    'placeholder' => 'Soyad',
                    'maxlength' => 30,
                ],
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'jobDesc',
                'label' => 'İş Pozisyonu',
                'placeholder' => 'Bir pozisyon seçin',
            ])
            ->add('jobLvl', IntegerType::class, [
                'label' => 'İş Seviyesi',
                'help' => '10 ile 250 arasında olmalıdır',
                'required' => false,
                'attr' => [
                    'min' => 10,
                    'max' => 250,
                ],
            ])
            ->add('publisher', EntityType::class, [
                'class' => Publisher::class,
                'choice_label' => fn (Publisher $pub) => $pub->getPubName().' ('.$pub->getPubId().')',
                'label' => 'Yayıncı',
                'placeholder' => 'Bir yayıncı seçin',
            ])
            ->add('hireDate', DateTimeType::class, [
                'label' => 'İşe Başlama Tarihi',
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
