<?php

namespace DSF\View\Helper\Form;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  FormRow 
{

	/**
	 * comments
	 */
	public function FormRow($label, $control, $required = false){
	    $class = null;
		if($required){$class = "required";}
		$xhtml[] = "<dt><label class='formRow {$class}'>{$label}</label></dt>";
		$xhtml[] = "<dd>{$control}</dd>";
	return implode(null,$xhtml);
	}
	
    /**
     * Set this->view object
     *
     * @param  Zend_this-> viewInterface  $this->view
     * @return Zend_this->view_Helper_DeclareVars
     */
    public function setview( viewInterface  $view)
    {
        $this->view = $view;
        return $this;
    }
}
