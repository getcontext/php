<?php

namespace School\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('short_desc')
            ->add('description')
            ->add('spec')
            ->add('price')
            //->add('category_id')
            //->add('file_id')
            ->add('category')
            ->add('file')
        ;
    }

    public function getName()
    {
        return 'school_appbundle_producttype';
    }
}
