<?php

namespace App\Form;

use App\Entity\Report;
use App\Entity\ReportCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => ReportCategory::class,
                'label' => 'Kategoria',
                'choice_label' => 'name'
            ])
            ->add('topic', TextType::class, [
                'label' => 'Temat'
            ])
            ->add('description', TextAreaType::class, [
                'label' => 'Opis',
                'attr' => [
                    'rows' => 5,
                    'cols' => 20
                ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Wyślij'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
