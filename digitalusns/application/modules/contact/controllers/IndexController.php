<?php

namespace Mod\Contact;








class  IndexController  extends \Zend_Controller_Action 
{
	
	
	public function init()
	{     
	    $this->view->breadcrumbs = array(
	       'Modules' =>   $this->getFrontController()->getBaseUrl() . '/admin/module',
	       'Contact' =>   $this->getFrontController()->getBaseUrl() . '/mod_contact'
	    ); 
	    $this->view->toolbarLinks[$this->view->GetTranslation('Add to my bookmarks')] = $this->getFrontController()->getBaseUrl() . '/admin/index/bookmark/url/mod_contact';
	    
	}
	
	public function indexAction()
	{
		
	}

}
