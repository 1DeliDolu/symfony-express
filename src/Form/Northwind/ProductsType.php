<?php

namespace App\Form\Northwind;

use App\Entity\Northwind\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('productName')
            ->add('supplierId')
            ->add('categoryId')
            ->add('quantityPerUnit')
            ->add('unitPrice')
            ->add('unitsInStock')
            ->add('unitsOnOrder')
            ->add('reorderLevel')
            ->add('discontinued')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
