<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserprofileType extends AbstractType
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
                'attr' => ['class' => 'form-control', 'readonly' => true]
            ])
            ->add('cellphone', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Phone number']
            ])
            ->add('profilePic', FileType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Upload Pic',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3m',
                        'mimeTypesMessage' => 'Please upload an image with size below 3 mb ',
                    ])
                ]
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
