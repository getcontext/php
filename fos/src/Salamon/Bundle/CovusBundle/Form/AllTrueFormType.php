<?php



namespace Salamon\Bundle\CovusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllTrueFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        for ($i = 1; $i <= 5; $i++) {
            $builder->add('val' . $i, 'checkbox', array(
                'label' => 'User yes/no ' . $i,
                'required' => false,
            ));
        }

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $data = $event->getData();

                $vals = array();
                $vals[] = $data->getVal1();
                $vals[] = $data->getVal2();
                $vals[] = $data->getVal3();
                $vals[] = $data->getVal4();
                $vals[] = $data->getVal5();
                foreach ($vals as $val) {
                    static $i = 0;
                    if (!empty($val)) {
                        $i++;
                    }
                }
                if ($i > 2) {
                    $form->addError(new FormError("Only 2 values are allowed !"));
                }

            }
        );
    }

    public function getName()
    {
        return 'fos_user_alltrue';
    }
}
