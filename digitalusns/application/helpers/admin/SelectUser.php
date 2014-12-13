<?php

namespace DSF\View\Helper\Admin;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  SelectUser 
{	
	public function SelectUser($name, $value = null, $attribs = null, $currentUser = 0)
	{
        $u = new \User();
        $users = $u->fetchAll(null, 'first_name');
        
        $userArray[] = $this->view->GetTranslation('Select \User');
        
        if($users->count() > 0) {
            foreach ($users as $user)
            {
                if($user->id != $currentUser) {
            	   $userArray[$user->id] = $user->first_name . ' ' . $user->last_name;
                }
            }
        }
   		return $this->view->formSelect($name, $value, $attribs, $userArray);
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
