<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Form\Type\TagsInputType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('title',null,[])
            ->add('title',TextType::class,[
                'required'=>'required',
                'attr'=>[
                    'class'=>'form-title form-control',
                    'placeholder'=>'Ingrese el títuo del post'
                ]
            ])
            ->add('content',TextareaType::class,[
                'required'=>'required',
                'attr'=>[
                    //'class'=>'form-content form-control tinymce',
                    'class'=>'form-content form-control',
                    'placeholder'=>'Ingrese el contenido del post'
                ]
            ])
            /*
            ->add('content',TextType::class,[
                'required'=>'required',
                'attr'=>[
                    'class'=>'form-content form-control',
                    'placeholder'=>'Ingrese el contenido del post'
                ]
            ])

            ->add('tags', TagsInputType::class, [
                'label' => 'Tags',

                'required' => false,
                'attr'=>[
                    'data-role'=>'tagsinput',
                    'class'=>'form-control'
                ]
            ])
            */
            ->add('isPublished', checkboxType::class,[
                //'attr' => ['class' => 'form-check form-control'],
                'required' => false,
                'label' => 'Publicar'
            ])

            //->add('createdAt')
            //->add('tags')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
