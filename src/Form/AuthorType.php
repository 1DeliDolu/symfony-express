<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'] ?? true;

        $builder
            ->add('auId', TextType::class, [
                'label' => 'Yazar ID',
                'help' => 'Format: XXX-XX-XXXX (örn: 123-45-6789)',
                'attr' => [
                    'placeholder' => '123-45-6789',
                    'pattern' => '\d{3}-\d{2}-\d{4}',
                    'maxlength' => 11,
                ],
                'disabled' => !$isNew,
            ])
            ->add('auLname', TextType::class, [
                'label' => 'Soyad',
                'help' => 'Yazarın soyadı',
                'attr' => [
                    'placeholder' => 'Soyad',
                    'maxlength' => 40,
                ],
            ])
            ->add('auFname', TextType::class, [
                'label' => 'Ad',
                'help' => 'Yazarın adı',
                'attr' => [
                    'placeholder' => 'Ad',
                    'maxlength' => 20,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telefon',
                'help' => 'Format: XXX XXX-XXXX veya "UNKNOWN"',
                'attr' => [
                    'placeholder' => '123 456-7890',
                    'maxlength' => 12,
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adres',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adres',
                    'maxlength' => 40,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Şehir',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Şehir',
                    'maxlength' => 20,
                ],
            ])
            ->add('state', TextType::class, [
                'label' => 'Eyalet',
                'help' => '2 harflik eyalet kodu (örn: CA)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'CA',
                    'pattern' => '[A-Z]{2}',
                    'maxlength' => 2,
                ],
            ])
            ->add('zip', TextType::class, [
                'label' => 'Posta Kodu',
                'help' => '5 rakam',
                'required' => false,
                'attr' => [
                    'placeholder' => '12345',
                    'pattern' => '\d{5}',
                    'maxlength' => 5,
                ],
            ])
            ->add('contract', CheckboxType::class, [
                'label' => 'Sözleşmeli',
                'help' => 'Yazar sözleşme imzaladı mı?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
            'is_new' => true,
        ]);
    }
}
