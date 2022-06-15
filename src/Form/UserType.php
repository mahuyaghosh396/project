<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',TextType::class,['attr' => ['class' => 'form-control','placeholder' => 'Enter your email']])
            //->add('roles')
            //->add('password')
            ->add('cellphone',TextType::class,['attr' => ['class' => 'form-control','placeholder' => 'Phone number']])
            ->add('academicYear',NumberType::class,['attr' => ['class' => 'form-control','placeholder' => 'Academic year'],'required'=>false])
            ->add('registrationNumber',TextType::class,['attr' => ['class' => 'form-control','placeholder' => 'Registration number'],'required'=>false])
            ->add('rollNumber',TextType::class,['attr' => ['class' => 'form-control','placeholder' => 'Roll number'],'required'=>false])
            // ->add('department')
            // ->add('profilePic')
            ->add('firstName',TextType::class,['attr' => ['class' =>'form-control','placeholder' => 'First name ']])
            ->add('lastName',TextType::class,['attr' => ['class' => 'form-control','placeholder' => 'Last name']])
            ->add('address',TextareaType::class,['attr' => ['class' => 'form-control','placeholder' => 'Address']])
            //->add('enable')
            ->add('dob',TypeDateType::class,['attr' => ['class' => 'form-control','placeholder' => 'Date of birth'],'required'=>false])
            //->add('status')
            //->add('created')
            //->add('updated')
            //->add('lastLogin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
