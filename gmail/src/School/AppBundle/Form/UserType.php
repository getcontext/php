<?php

namespace School\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    	$s = null;
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('password', 'password')
            ->add('repassword', 'password')
        ;
    }

    public function getName()
    {
        return 'school_appbundle_usertype';
    }
}
