<?php

namespace DSF\View\Helper\Cms;


/**
 * this helper script renders one \of the public cms module pages
 * these are mapped to 
 * /application/modules/{module name}/views/scripts/public/{script name}.phtml
 * you can optionally send this helper an array \of parameters
 * these will be added to the view object and can be retrieved later by going like:
 * $param = $this->view->{module name}->{param}
 *
 */


use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderModuleScript 
{	
	public function RenderModuleScript($module, $script, $params = false)
	{
		if(is_array($params))
		{
			$this->view->$module = new \stdClass();
			$mdl = $this->view->$module;
			foreach ($params as $k => $v)
			{
				$mdl->$k = $v;
			}
		}
		$this->view->addScriptPath('./application/modules/' . $module . '/views/scripts/public');
		return $this->view->render($script . '.phtml');
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
