<?php

namespace App\Form;

use App\Dto\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone number',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 100,
                    ]),
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirm password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                ],
            ])
            ->add('postCode', null, [
                'label' => 'Post code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a post code',
                    ]),
                ],
            ])
            ->add('city', null, [
                'label' => 'City',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a city',
                    ]),
                ],
            ])
            ->add('street', null, [
                'label' => 'Street',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a street',
                    ]),
                ],
            ])
            ->add('number', null, [
                'label' => 'House number',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a house number',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
