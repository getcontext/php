<?php

namespace School\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
//             ->add('generated_name')
//             ->add('extension')
//             ->add('size')
//             ->add('path')
            ->add('file')
        ;
    }

    public function getName()
    {
        return 'gallery_appbundle_filetype';
    }
}
