<?php

namespace DSF\View\Helper\Navigation;


/**
 * this helper will render any submenu relative to the page path
 * the first level menu is your top level menu (those pages in the site root)
 *
 */


use Zend\view\viewInterface as viewInterface;
use DSF\Toolbox\ToolboxString as ToolboxString;
use view\viewInterface as viewInterface;




class  RenderMenuByLevel 
{	
	public function RenderMenuByLevel($level = 1, $levels = 1, $id = 'nav')
	{
	    if($level == 1)  {
	        $menu->id = 0; 
	    }else{
    	    $level = $level - 2;
    
    	    
    	    //add parents
    		$parents = $this->view->pageObj->getParents();
    		if(is_array($parents)){
    		    $pages = $parents;
    		}
    		//in this case the current page has to be considered as well if it is not the root page
    		$currentPage = $this->view->page;
    		if($currentPage->id > 0){
    		    $pages[] = $currentPage;
    		}
    		
    		if(is_array($pages) && isset($pages[$level]))
    		{
    			$menu = $pages[$level];
    		}else{
    		    return false;
    		}
    
            //build the page path
            $uriParts = array_chunk($pages, $level + 1);
            foreach ($uriParts[0] as $page) {
            	$label = $this->getLabel($page);
            	$pathParts[] =  ToolboxString ::addHyphens($label);
            }
            
            if(is_array($pathParts)){
                $path = implode('/', $pathParts);
            }
        	
	        
	    }	
   		return $this->view->RenderMenu($menu->id, $levels, 0, '/' . $path , $id);
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
    
    public function getLabel($page){
        if(!empty($page->label))
		{
			return $page->label;
		}else{
			return $page->title;
		}
    }
}
