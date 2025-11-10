<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'] ?? true;

        $builder
            ->add('storId', TextType::class, [
                'label' => 'Mağaza ID',
                'help' => '4 karakter',
                'attr' => [
                    'placeholder' => '1234',
                    'maxlength' => 4,
                ],
                'disabled' => !$isNew,
            ])
            ->add('storName', TextType::class, [
                'label' => 'Mağaza Adı',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Mağaza adı',
                    'maxlength' => 40,
                ],
            ])
            ->add('storAddress', TextType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Store::class,
            'is_new' => true,
        ]);
    }
}
