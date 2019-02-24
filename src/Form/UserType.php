<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 05/02/19
 * Time: 17:23
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
                'required'=>'required',
                'attr'=>[
                    'class'=>'form-username form-control',
                    'placeholder'=>'Ingrese su nombre de usuario'
                ]
            ])
            ->add('email', EmailType::class,[
                'required'=>'required',
                'attr'=>[
                    'class'=>'form-email form-control',
                    'placeholder'=>'Ingrese su correo electrónico'
                ]
            ])
            ->add('plainPassword',RepeatedType::class,[
                'type'=>PasswordType::class,
                'required'=>'required',
                'invalid_message' => 'Las contraseñas deben ser iguales',
                'first_options'=>[
                    'attr'=>[
                        'class'=>'form-password form-control',
                        'placeholder'=>'Ingrese su contraseña'
                    ]
                ],
                'second_options'=>[
                    'attr'=>[
                        'class'=>'form-password form-control',
                        'placeholder'=>'Repita su contraseña'
                    ]
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>'App\Entity\User']);
    }
}