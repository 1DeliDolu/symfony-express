<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Author;
use App\Entity\Pubs\Title;
use App\Entity\Pubs\TitleAuthor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleAuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', EntityType::class, [
                'class' => Title::class,
                'choice_label' => 'title',
                'label' => 'Buch',
                'placeholder' => 'Wählen Sie ein Buch',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => fn(Author $author): string => $author->getAuFname() . ' ' . $author->getAuLname(),
                'label' => 'Autor',
                'placeholder' => 'Wählen Sie einen Autor',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('auOrd', IntegerType::class, [
                'label' => 'Reihenfolge',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'z.B. 1, 2, 3...',
                    'min' => 1,
                ],
            ])
            ->add('royaltyPer', IntegerType::class, [
                'label' => 'Tantiemen (%)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0-100',
                    'min' => 0,
                    'max' => 100,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TitleAuthor::class,
        ]);
    }
}
