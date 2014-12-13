<?php

namespace DSF\Controller;




use Zend\Registry as Registry;
use DSF\Page as DSFPage;



 

class  Action  extends \Zend_Controller_Action 
{
	public $page;

	public function init()
	{
		$this->_helper->removeHelper('viewRenderer');
		if( Registry ::isRegistered('page')){
			$this->page =  Registry ::get('page');
		}else{
			$this->page = new  DSFPage ();
			$this->_registerPage();
		}
	}
	
	protected function _registerPage()
	{
		 Registry ::set('page', $this->page);
	}
    
}
