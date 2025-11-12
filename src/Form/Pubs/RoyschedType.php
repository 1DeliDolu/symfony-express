<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Roysched;
use App\Entity\Pubs\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoyschedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lorange')
            ->add('hirange')
            ->add('royalty')
            ->add('title', EntityType::class, [
                'class' => Title::class,
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Roysched::class,
        ]);
    }
}
