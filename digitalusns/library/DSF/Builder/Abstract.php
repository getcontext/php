<?php

namespace DSF\Builder;





use Zend\Registry as Registry;
use DSF\Page as DSFPage;



abstract class  BuilderAbstract 
{
	protected $_page;
	
	public function __construct()
	{
		if( Registry ::isRegistered('page')) {
			$this->_page =  Registry ::get('page');
		}else{
			$this->_page = new  DSFPage ();
			$this->_registerPage();
		}
		//fire the init function
		$this->init();
	}
	
	public function init()
	{
		
	}
	
	public function getPage()
	{
		return $this->_page;
	}
	
	protected function _registerPage()
	{
		 Registry ::set('page', $this->_page);
	}
}
