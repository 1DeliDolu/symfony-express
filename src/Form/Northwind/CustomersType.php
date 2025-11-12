<?php

namespace App\Form\Northwind;

use App\Entity\Northwind\Customers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerId', TextType::class, [
                'label' => 'Customer ID',
                'attr' => ['maxlength' => 5]
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Company Name',
                'attr' => ['maxlength' => 40]
            ])
            ->add('contactName', TextType::class, [
                'label' => 'Contact Name',
                'required' => false,
                'attr' => ['maxlength' => 30]
            ])
            ->add('contactTitle', TextType::class, [
                'label' => 'Contact Title',
                'required' => false,
                'attr' => ['maxlength' => 30]
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
                'required' => false,
                'attr' => ['maxlength' => 60]
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => false,
                'attr' => ['maxlength' => 15]
            ])
            ->add('region', TextType::class, [
                'label' => 'Region',
                'required' => false,
                'attr' => ['maxlength' => 15]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Postal Code',
                'required' => false,
                'attr' => ['maxlength' => 10]
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
                'required' => false,
                'attr' => ['maxlength' => 15]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'required' => false,
                'attr' => ['maxlength' => 24]
            ])
            ->add('fax', TextType::class, [
                'label' => 'Fax',
                'required' => false,
                'attr' => ['maxlength' => 24]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customers::class,
        ]);
    }
}
