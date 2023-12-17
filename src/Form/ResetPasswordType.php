<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Hasło',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Wpisz swoje hasło',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Hasło musi mieć co najmniej {{ limit }} znaków',
                        'max' => 4096,
                    ]),
                ],
                'toggle' => true
            ])
            ->add('repeatPassword', PasswordType::class, [
                'label' => 'Powtórz hasło',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Pole nie może być puste',
                    ]),
                ],
            ])
            ->add('Zapisz', SubmitType::class);
    }
}