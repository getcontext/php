<?php

namespace Zend\View\Helper;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  SelectParentPage 
{	
	public function SelectParentPage($name, $value=null, $attribs = null)
	{
        $mdlIndex = new \Page();
        $index = $mdlIndex->getIndex();
    	$index[0] = 'Site Root';  
    	
		return $this->view->formSelect($name, $value, $attribs, $index);
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
