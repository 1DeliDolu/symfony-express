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
                'label' => 'Autoren-ID',
                'help' => 'Format: XXX-XX-XXXX (z. B.: 123-45-6789)',
                'attr' => [
                    'placeholder' => '123-45-6789',
                    'pattern' => '\d{3}-\d{2}-\d{4}',
                    'maxlength' => 11,
                ],
                'disabled' => !$isNew,
            ])
            ->add('auLname', TextType::class, [
                'label' => 'Nachname',
                'help' => 'Nachname des Autors',
                'attr' => [
                    'placeholder' => 'Nachname',
                    'maxlength' => 40,
                ],
            ])
            ->add('auFname', TextType::class, [
                'label' => 'Vorname',
                'help' => 'Vorname des Autors',
                'attr' => [
                    'placeholder' => 'Vorname',
                    'maxlength' => 20,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telefon',
                'help' => 'Format: XXX XXX-XXXX oder "UNKNOWN"',
                'attr' => [
                    'placeholder' => '123 456-7890',
                    'maxlength' => 12,
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse',
                    'maxlength' => 40,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Stadt',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Stadt',
                    'maxlength' => 20,
                ],
            ])
            ->add('state', TextType::class, [
                'label' => 'Bundesstaat',
                'help' => '2-stelliger Bundesstaat-Code (z. B.: CA)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'CA',
                    'pattern' => '[A-Z]{2}',
                    'maxlength' => 2,
                ],
            ])
            ->add('zip', TextType::class, [
                'label' => 'Postleitzahl',
                'help' => '5 Ziffern',
                'required' => false,
                'attr' => [
                    'placeholder' => '12345',
                    'pattern' => '\d{5}',
                    'maxlength' => 5,
                ],
            ])
            ->add('contract', CheckboxType::class, [
                'label' => 'Vertraglich',
                'help' => 'Hat der Autor den Vertrag unterschrieben?',
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
