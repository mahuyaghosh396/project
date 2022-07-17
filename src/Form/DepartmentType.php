<?php

namespace App\Form;

use App\Entity\Department;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Department name']
            ])
            ->add('code',TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Department code']
            ])
            //->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}
