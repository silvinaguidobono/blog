<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 16/02/19
 * Time: 16:17
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class AdminUserType extends AbstractType
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
            ->add('roles', choiceType::class,[
                'required' => 'required',
                'multiple' => true,
                //'expanded' => true,
                'choices' => [
                    'Administrator' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER'
                ],
                //'empty_data' => 'ROLE_USER',
                'attr' => ['class' => 'form-check form-control'],
                'label' => 'Roles'
            ])

            ->add('isActive', checkboxType::class,[
                //'attr' => ['class' => 'form-check form-control'],
                'required' => false,
                'label' => 'Activo'
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