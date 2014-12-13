<?php

namespace DSF\View\Helper\Content;




use Zend\view\viewInterface as viewInterface;
use DSF\Toolbox\ToolboxString as ToolboxString;
use view\viewInterface as viewInterface;




class  ListWhatsNew 
{
	/**
	 * render a module page like news_showNewPosts
	 */
	public function ListWhatsNew(){
		$newStories = $this->view->pageObj->getNewStories();
		if($newStories){
		    foreach ($newStories as $story){
		        $link =  ToolboxString ::addHyphens($this->view->RealPath($story->id));
		        $data[] = "<a href='{$link}'>" . $this->view->pageObj->getLabel($story) . "</a>";
		    }
		    if(is_array($data)){
		        return $this->view->htmlList($data);
		    }
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
