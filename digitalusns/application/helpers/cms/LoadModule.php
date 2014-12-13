<?php

namespace Zend\View\Helper;




use Zend\view\viewInterface as viewInterface;
use DSF\Filesystem\Dir as Dir;
use view\viewInterface as viewInterface;




class  LoadModule 
{
	/**
	 * render a module page like news_showNewPosts
	 */
	public function LoadModule($module, $action, $params = null){		
		//validate the module
		$modules =  Dir ::getDirectories('./application/modules');
		
		// @todo: validate the action as well
		if(in_array($module, $modules))
		{
		    if(is_array($params))
		    {
    		    foreach ($params as $k => $v)
    		    {
    		        $paramsArray[(string)$k] = (string)$v;
    		    }
		    }
            return $this->view->action($action, 'public', 'mod_' . $module, $paramsArray);
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
