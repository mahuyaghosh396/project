<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('cellphone')
            ->add('academicYear')
            ->add('registrationNumber')
            ->add('rollNumber')
            ->add('department')
            ->add('profilePic')
            ->add('firstName')
            ->add('lastName')
            ->add('address')
            ->add('enable')
            ->add('dob')
            ->add('status')
            ->add('created')
            ->add('updated')
            ->add('lastLogin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
