<?php

namespace App\Form;

use App\Entity\Notice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as TypeDateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoticeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('noticeFrom', TypeDateTimeType::class,['attr'=> ['class' => 'form-control'],'label' => 'From'])
            ->add('noticeTo', TypeDateTimeType::class,['attr'=> ['class' => 'form-control'],'label' => 'To'])
            ->add('file',FileType::class,[ 
                'attr' => ['class' => 'form-control'],
                'label' => 'Upload File (PDF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                      ])
                    ]
                ])
            // ->add('status')
            // ->add('created')
            // ->add('updated')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notice::class,
        ]);
    }
}
