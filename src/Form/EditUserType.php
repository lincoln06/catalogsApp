<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => [
                        'Administrator' => ['ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_USER'],
                        'Edytor' => ['ROLE_EDITOR', 'ROLE_USER'],
                        'Użytkownik' => ['ROLE_USER'],
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'choice_label' => function($value, $key, $index)
                    {
                        if($key === 0)
                        {
                            return 'Administrator';
                        } elseif($key === 1) {
                            return 'Edytor';
                        } else {
                            return 'Użytkownik';
                        }
                    }
                ])
            ->add('Zapisz', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
