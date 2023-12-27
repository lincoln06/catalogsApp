<?php

namespace App\Form;

use App\Entity\Catalog;
use App\Entity\System;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class CatalogType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('system', EntityType::class, [
                'class' => System::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Pole nie może być puste',
                    ]),
                ]])
            ->add('dateAdded', DateType::class, [
                'label' => 'Data wydania',
            ])
            ->add('pdfFile', FileType::class, [
                'label' => 'Plik (max. 200 MB, format PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '200M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Plik nie jest prawidłowm plikiem PDF',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Zapisz'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Catalog::class,
        ]);
    }
}
