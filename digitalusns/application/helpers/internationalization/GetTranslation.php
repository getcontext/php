<?php

namespace DSF\View\Helper\Internationalization;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  GetTranslation 
{
  
    /**
     * this helper returns the translation \for the passed key
     * it will optionally add the controller
     * and action to the key
     * 
     * example: controller_action_page_title
     *
     * @return unknown
     */
	public function GetTranslation($key, $locale = null,$viewInstance = null)
	{
		if($viewInstance !== null) {
			$this->setview($viewInstance);	
		}
		
	    if($locale != null) {
	        $this->view->translate->setLocale($locale);
	    }
        return $this->view->translate($key);
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
