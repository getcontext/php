<?php

namespace DSF\View\Helper\General;


/**
 * this helper renders the current file's image attachment
 * if it does not have one then it goes up the line
 *
 */


use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  InheritImage 
{	
	public function InheritImage()
	{
	    if(empty($this->view->page->filepath)){
    		$parents = $this->view->pageObj->getParents('ASC');
    		if(is_array($parents)){
    		    foreach ($parents as $parent){
    		        if(!empty($parent->filepath)){
    		            return $this->renderImage($parent->filepath);
    		        }
    		    }
    		}
	    }else{
	        return $this->renderImage($this->view->page->filepath);        
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
    
    public function renderImage($filepath){
        return "<img src='/{$filepath}' class='reflect' />";
    }
}
