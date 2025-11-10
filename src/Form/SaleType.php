<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Sale;
use App\Entity\Store;
use App\Entity\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ordNum')
            ->add('ordDate')
            ->add('qty')
            ->add('payterms')
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => 'id',
            ])
            ->add('title', EntityType::class, [
                'class' => Title::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}
