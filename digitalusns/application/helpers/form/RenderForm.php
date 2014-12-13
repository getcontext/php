<?php

namespace DSF\View\Helper\Form;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderForm 
{

	/**
	 * comments
	 */
	public function RenderForm($action, $rows = array(), $submitText = 'Save Changes', $multipart = false){
		if($multipart)
		{
			$encType = "enctype='multipart/form-data'";
		}
		$xhtml = "<form action='{$action}' method='post' {$encType} >";
		$xhtml .= implode(null, $rows);
		$xhtml .= $this->view->formSubmit(str_replace(' ', '_', $submitText), $submitText);
		$xhtml .= "</form>";
		return $xhtml;
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
