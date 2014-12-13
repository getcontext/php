<?php

namespace Zend\View\Helper;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderSidebar 
{

	/**
	 * comments
	 */
	public function RenderSidebar(){
		$path = str_replace('.phtml', '.sidebar.phtml', $this->view->actionScript);
		
		return $this->view->render($path);
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
