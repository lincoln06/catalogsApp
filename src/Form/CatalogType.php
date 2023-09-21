<?php

namespace App\Form;

use App\Entity\Catalog;
use App\Entity\System;
use App\Repository\SystemRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CatalogType extends AbstractType
{
    private SystemRepository $systemRepository;
    public function __construct(SystemRepository $systemRepository) {
        $this->systemRepository = $systemRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('system', EntityType::class,[
                'class' => System::class,
                'choice_label' => 'name',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nazwa'
            ])
            ->add('dateAdded', DateType::class, [
                'label' => 'Data wydania'
            ])
            ->add('pdfFile', FileType::class, [
                'label' => 'Plik',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '200M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Plik nie jest prawidłowm plikiem PDF',
                    ])
                ],
            ])
            ->add('Zapisz', SubmitType::class)
            // ...
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Catalog::class,
        ]);
    }
}
