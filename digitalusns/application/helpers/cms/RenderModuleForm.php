<?php

namespace Zend\View\Helper;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderModuleForm 
{

	/**
	 * comments
	 */
	public function RenderModuleForm($module, $action, $parameters){
	    $dir = './application/modules/' . $module . '/views/scripts';
	    $helpers = './application/modules/' . $module . '/views/helpers';
		$path = "/public/" . $action . ".form.phtml";
		$fullPath = $dir . $path;
	    if(file_exists($fullPath))
	    {
    	    $this->view->addScriptPath($dir);
    	    $this->view->addHelperPath($helpers);
    		$this->view->formParams = $parameters;
		      return $this->view->render($path);
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
