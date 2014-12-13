<?php

namespace School\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    	$s = null;
        $builder
            ->add('name')
            ->add('description')
            //->add('parent_id')
//             ->add('files', 'entity', array(
//             		'class' => 'SchoolAppBundle:File',
//             		'query_builder' => function(EntityRepository $er) use ($s) {
//             		return $er->createQueryBuilder('c')
//             		->orderBy('c.name', 'ASC');
//             },
//             'property' => "img",
//             'multiple' => true,
//             'expanded' => true,
//             ));
         
        ;
    }

    public function getName()
    {
        return 'school_appbundle_categorytype';
    }
}
