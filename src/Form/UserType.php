<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    private $academicYear;

    public function __construct()
    {
        for ($i = date('Y') - 5; $i < date('Y') + 5; $i++) {
            $this->academicYear[] = $i;
        }
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your email']
            ])
            ->add('cellphone', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Phone number']
            ])
            ->add('academicYear', ChoiceType::class, [
                'choices' => array_combine($this->academicYear, $this->academicYear),
                'attr' => ['class' => 'form-control']
            ])
            ->add('registrationNumber', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Registration number']
            ])
            ->add('rollNumber', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Roll number']
            ])
            // ->add('department', ChoiceType::class, [
            //     'choices' => [
            //         '' => '',
            //         'CST' => 'ARCH',
            //         'ETCE' => 'ETCE',
            //         'ARCH' => 'ARCH',
            //     ],
            //     'attr' => ['class' => 'form-control']
            // ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'Student' => 'ROLE_STUDENT',
                    'Lecturer' => 'ROLE_LECTURER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'First name ']
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Last name']
            ])
            ->add('address', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Address']
            ])
            ->add('dob', TypeDateType::class, [
                'widget' => 'single_text',
                'required' => false,
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker form-control'],
                'format' => 'dd-MM-yyyy'
            ])

            ->add('enable', ChoiceType::class, [
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'Active',
                    'Deleted' => 'Deleted',
                ],
                'attr' => ['class' => 'form-control']
            ]);

        // Data transformer
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
