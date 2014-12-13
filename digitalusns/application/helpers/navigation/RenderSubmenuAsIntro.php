<?php

namespace DSF\View\Helper\Navigation;




use Zend\view\viewInterface as viewInterface;
use DSF\Toolbox\ToolboxString as ToolboxString;
use view\viewInterface as viewInterface;




class  RenderSubmenuAsIntro 
{	
	public function RenderSubmenuAsIntro($id = 'subnav')
	{
		$parents = $this->view->pageObj->getParents();
		if(is_array($parents) && count($parents) > 0)
		{
			$parent = $parents[0];
			$subMenu = $parent;
		}
		
		if($subMenu < 1)
		{
			$subMenu = $this->view->page;
		}
		
		$page = new ContentPage();
		$children = $this->view->pageObj->getChildren($subMenu->id);
		$basePath = '/' .  ToolboxString ::addHyphens($subMenu->label);
				
		foreach ($children as $child)
		{
			
			$link = $basePath . '/' .  ToolboxString ::addHyphens($child->title);
			$linkId =  ToolboxString ::addUnderscores($page->path, true);
			$subPages[] ="<h3><a href='{$link}' class='{$class}' id='page-{$child->id}'>{$child->title}</a></h3><p>" . $this->view->TruncateText($child->content, 15) . '</p>';
		}
		
		if($subPages)
		{
			return "<div id='{$id}'>" . implode(null, $subPages) . "</div>";
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
