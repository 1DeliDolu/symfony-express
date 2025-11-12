<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-posta',
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Lütfen bir e-posta adresi girin.']),
                    new Assert\Email(['message' => 'Geçerli bir e-posta adresi girin.']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Parola',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Parolayı tekrar girin',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'invalid_message' => 'Girilen parolalar eşleşmiyor.',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Lütfen bir parola belirleyin.']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Parola en az {{ limit }} karakter olmalıdır.',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Kullanım şartlarını kabul ediyorum',
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue(['message' => 'Devam etmek için şartları kabul etmelisiniz.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // CSRF korumasını etkinleştir (varsayılan olarak zaten true)
            'csrf_protection' => true,
            // Gizli CSRF alanının adı
            'csrf_field_name' => '_token',
            // Token ID - her form için benzersiz olmalı (güvenlik için)
            'csrf_token_id' => 'user_registration',
        ]);
    }
}
