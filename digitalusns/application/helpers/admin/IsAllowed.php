<?php

namespace DSF\View\Helper\Admin;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;
use Zend\Registry as Registry;




class   IsAllowed 
{

	/**
	 * comments
	 */
	public function IsAllowed($output, $module, $controller = null, $action = null){
	    $role = 'admin';
		$acl =  Registry ::get('acl');
		//go from more specific to less specific
		$moduleLevel = $module;
		$controllerLevel = $moduleLevel . '_' . $controller;
		$actionLevel = $controllerLevel . '_' . $action;
		
		if (null != $action && $acl->has($actionLevel)) {
			$resource = $actionLevel;
		}elseif (null != $controller && $acl->has($controllerLevel)){
		    $resource = $controllerLevel;
		}else{
		    $resource = $moduleLevel;
		}
		
		if($acl->has($resource)) {
		    if($acl->isAllowed($role, $resource)) {		        
		        return $output;
		    }
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
			
