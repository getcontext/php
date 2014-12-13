<?php
/**
 * the public index controller's sole mission in life is to render content pages
 *
 */
class \IndexController extends \Zend_Controller_Action
{
	public $page;
	
	public function init()
	{
	}
	
	public function indexAction()
	{
	    if(!$this->view->content) {	
    		\DSF\Builder::loadPage();
    		$this->page = \DSF\Builder::getPage();
    		$this->view->page = $this->page;
    		$this->view->layout()->page = $this->view->render('layouts/' . $this->page->getLayout());
	    }
	}
	
	public function renderPageAction()
	{
	    
	}
}


