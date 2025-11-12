<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Discount;
use App\Entity\Pubs\Store;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discountType')
            ->add('lowQty')
            ->add('highQty')
            ->add('discount')
            ->add('store', EntityType::class, [
                'class' => Store::class,
                'choice_label' => 'storName',
                'required' => false,
                'placeholder' => 'Geschäft auswählen...',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discount::class,
        ]);
    }
}
