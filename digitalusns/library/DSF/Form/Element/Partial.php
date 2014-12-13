<?php

namespace DSF\Form\Element;



require_once ('Zend/Form/Element.php');


use DSF\Form\Element\Xml as FormElementXml;





class  Partial  extends  FormElementXml   {
    
    public $partial;
    
    public function init()
    {
        $this->setDecorators(array(array("ViewScript", array(
            'viewScript'	=> $this->partial,
            'class'	=>    'partial'
        ))));
    }
    
    public function setPartial($script)
    {
        $this->partial = $script;
    }
}

?>
