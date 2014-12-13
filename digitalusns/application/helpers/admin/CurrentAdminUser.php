<?php

namespace DSF\View\Helper\Admin;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class   CurrentAdminUser 
{

	/**
	 * comments
	 */
	public function CurrentAdminUser($id = 'currentUser'){
		$u = new \User();
		$user = $u->getCurrentUser();

		if($user)
		{
			$xhtml = "<ul id='{$id}'>
					<li>" . $this->view->GetTranslation('Current \User') . ": {$user->first_name}  {$user->last_name}</li>
					<li>" . $this->view->GetTranslation('Role') . ": {$user->role}</li>
					<li><a href='{$this->view->baseUrl}/admin/auth/logout/'>" . $this->view->GetTranslation('Log Out') . "</a></li>
				</ul>";
			return $xhtml;
		}else{
		    return false;
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
			
