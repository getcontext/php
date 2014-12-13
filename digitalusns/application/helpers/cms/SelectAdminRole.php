<?php

namespace Zend\View\Helper;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  SelectAdminRole 
{	
	public function SelectAdminRole($name, $value, $attribs = false)
	{
		$data['admin'] = "Site Administrator";
		$data['superadmin'] = "Super Administrator";
		return $this->view->formSelect($name, $value, $attribs, $data);
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
