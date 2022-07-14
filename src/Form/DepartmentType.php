<?php

namespace App\Form;

use App\Entity\Department;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TypeTextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'name']
            ])
            ->add('code',TypeTextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'code']
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
