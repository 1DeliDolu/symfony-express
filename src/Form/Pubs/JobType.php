<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jobDesc', TextType::class, [
                'label' => 'İş Tanımı',
                'help' => 'İş pozisyonunun tanımı',
                'attr' => [
                    'placeholder' => 'İş pozisyonu tanımı',
                    'maxlength' => 50,
                ],
            ])
            ->add('minLvl', IntegerType::class, [
                'label' => 'Minimum Seviye',
                'help' => '10 ile 250 arasında olmalıdır',
                'attr' => [
                    'min' => 10,
                    'max' => 250,
                ],
            ])
            ->add('maxLvl', IntegerType::class, [
                'label' => 'Maximum Seviye',
                'help' => '10 ile 250 arasında olmalıdır',
                'attr' => [
                    'min' => 10,
                    'max' => 250,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
