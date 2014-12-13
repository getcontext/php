<?php

namespace Zend\View\Helper;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;
use Zend\Registry as Registry;
use Zend\View as View;




class  RenderApplet 
{

	/**
	 * comments
	 */
	public function RenderApplet($applet){
	    $config =  Registry ::get('config');
	    
	    //create a new instance \of view
	    $appletView = new  View ();
	    $appletView->setScriptPath($config->view->applet->path . '/' . $applet);
	    $appletView->setHelperPath($config->view->applet->path . '/' . $applet, 'DSF_Applet');
	    
	    //tell the applet about where it is
	    $appletView->page = $this->view->page;
	    $appletView->pageObj = $this->view->pageObj;
	    
	    //run the code behind
	    if(file_exists($config->view->applet->path . '/' . $applet . '/' . $applet . '.php')){
	       $appletView->$applet();
	    }
	    return $appletView->render($applet . '.phtml');
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
