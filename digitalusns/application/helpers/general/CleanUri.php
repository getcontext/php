<?php

namespace DSF\View\Helper\General;




use Zend\view\viewInterface as viewInterface;
use DSF\Toolbox\ToolboxString as ToolboxString;
use view\viewInterface as viewInterface;




class  CleanUri 
{
	/**
	 * removes any params from the uri
	 */
	public function CleanUri($uri = null, $absolute = false, $stripUnderscores = false){
        if($uri == null) {
	       $uri = $this->view->pageObj->getCleanUri();
        }
	    if($absolute && !empty($uri)){
	        $uri = '/' . $uri;
	    }
	    
	    if($stripUnderscores){
	        $uri =  ToolboxString ::stripUnderscores($uri, true);
	    }
        return   ToolboxString ::addHyphens($uri);
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
