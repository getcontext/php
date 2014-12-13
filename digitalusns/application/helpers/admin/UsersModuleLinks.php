<?php

namespace DSF\View\Helper\Admin;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;





class   UsersModuleLinks 
{

	/**
	 * comments
	 */
	public function UsersModuleLinks($id = 'moduleList'){
		$u = new \User();
		$modules = $u->getCurrentUsersModules();
			if($modules) {
			foreach ($modules as $module) {
					$moduleLinks[] = "<a href='/mod_{$module}/index' class='{$module}'>{$module}</a>";	
			}
		}
		if(is_array($moduleLinks))
		{
			return $this->view->HtmlList($moduleLinks, null, array('id' => $id), false);
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
			
