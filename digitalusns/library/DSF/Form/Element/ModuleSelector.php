<?php

namespace DSF\Form\Element;



require_once ('Zend/Form/Element.php');


use DSF\Form\Element\Xml as FormElementXml;





class  ModuleSelector  extends  FormElementXml   {
    
    public function init()
    {
        $this->setDecorators(array(array("ViewScript", array(
            'viewScript'	=> 'module/partials/load-module.phtml',
            'class'	=>    'partial'
        ))));
    }    
}

?>
