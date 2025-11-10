<?php

namespace App\Form;

use App\Entity\Publisher;
use App\Entity\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titleId')
            ->add('title')
            ->add('type')
            ->add('price')
            ->add('advance')
            ->add('royalty')
            ->add('ytdSales')
            ->add('notes')
            ->add('pubdate')
            ->add('publisher', EntityType::class, [
                'class' => Publisher::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Title::class,
        ]);
    }
}
