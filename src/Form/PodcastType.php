<?php

namespace App\Form;

use App\Entity\Podcast;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PodcastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', null, [
                'label' => 'Título',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add(
                'descripcion',
                null,
                [
                    'label' => 'Descripción',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            )
            ->add(
                'audio',
                FileType::class,
                [
                    'label' => 'Audio',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ],
                [
                    'contraints' => [
                        new File([
                            'mimeTypes' => [
                                'audio/*',
                            ],
                            'mimeTypesMessage' => 'Por favor, sube un audio válido',
                        ])
                    ]
                ]
            )
            ->add(
                'imagen',
                FileType::class,
                [
                    'label' => 'Imagen',
                    'attr' => [
                        'class' => 'form-control mb-3'
                    ],
                ],
                [
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Por favor, sube una imagen válida',
                        ])
                    ]
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'Crear Podcast',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Podcast::class,
        ]);
    }
}
