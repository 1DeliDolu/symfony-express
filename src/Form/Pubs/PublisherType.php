<?php

declare(strict_types=1);

namespace App\Form\Pubs;

use App\Entity\Pubs\Publisher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublisherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'] ?? true;

        $builder
            ->add('pubId', TextType::class, [
                'label' => 'Yayıncı ID',
                'help' => '4 karakter (örn: 1234)',
                'attr' => [
                    'placeholder' => '1234',
                    'maxlength' => 4,
                ],
                'disabled' => !$isNew,
            ])
            ->add('pubName', TextType::class, [
                'label' => 'Yayıncı Adı',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Yayın evi adı',
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
            ->add('country', TextType::class, [
                'label' => 'Ülke',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ülke',
                    'maxlength' => 30,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publisher::class,
            'is_new' => true,
        ]);
    }
}
