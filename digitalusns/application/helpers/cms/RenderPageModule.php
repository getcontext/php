<?php

namespace DSF\View\Helper\Cms;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderPageModule 
{
	public function RenderPageModule()
	{
		$module = $this->view->pageData->module_page;
		$parts = explode('/', $module);
		if(count($parts) == 2)
		{
			return $this->view->RenderModuleScript($parts[0], $parts[1]);
		}
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
