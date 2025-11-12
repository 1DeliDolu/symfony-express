<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Publisher;
use App\Entity\Pubs\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titleId', TextType::class, [
                'label' => 'Title ID',
                'help' => 'Format: 2 uppercase letters followed by 4 digits (e.g., BU1032)',
                'attr' => [
                    'placeholder' => 'BU1032',
                    'maxlength' => 6,
                    'pattern' => '[A-Z]{2}[0-9]{4}',
                ],
                'disabled' => !$options['is_new'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
                'help' => 'The name of the book or publication',
                'attr' => [
                    'placeholder' => 'Enter title name',
                    'maxlength' => 80,
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'help' => 'Category or genre of the title',
                'choices' => [
                    'Undecided' => 'UNDECIDED',
                    'Business' => 'business',
                    'Modern Cooking' => 'mod_cook',
                    'Popular Computing' => 'popular_comp',
                    'Psychology' => 'psychology',
                    'Traditional Cooking' => 'trad_cook',
                ],
                'placeholder' => 'Select a type',
            ])
            ->add('publisher', EntityType::class, [
                'class' => Publisher::class,
                'label' => 'Publisher',
                'help' => 'Select the publisher for this title',
                'choice_label' => fn (Publisher $publisher): string => sprintf(
                    '%s (%s)',
                    $publisher->getPubName() ?? 'Unknown',
                    $publisher->getPubId()
                ),
                'placeholder' => 'Select a publisher',
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'help' => 'Sale price of the title',
                'currency' => 'USD',
                'required' => false,
                'attr' => [
                    'placeholder' => '0.00',
                ],
            ])
            ->add('advance', MoneyType::class, [
                'label' => 'Advance',
                'help' => 'Advance payment to author',
                'currency' => 'USD',
                'required' => false,
                'attr' => [
                    'placeholder' => '0.00',
                ],
            ])
            ->add('royalty', IntegerType::class, [
                'label' => 'Royalty (%)',
                'help' => 'Royalty percentage (0-100)',
                'required' => false,
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'max' => 100,
                ],
            ])
            ->add('ytdSales', IntegerType::class, [
                'label' => 'Year-to-Date Sales',
                'help' => 'Total sales for the current year',
                'required' => false,
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                ],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'help' => 'Additional notes or comments (max 200 characters)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter any additional notes',
                    'maxlength' => 200,
                    'rows' => 3,
                ],
            ])
            ->add('pubdate', DateTimeType::class, [
                'label' => 'Publication Date',
                'help' => 'Date when the title was or will be published',
                'widget' => 'single_text',
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Title::class,
            'is_new' => true,
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
    }
}
