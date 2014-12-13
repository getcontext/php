<?php

namespace DSF\View\Helper\Navigation;




use Zend\view\viewInterface as viewInterface;
use DSF\Toolbox\ToolboxString as ToolboxString;
use view\viewInterface as viewInterface;




class  RenderBreadcrumbs 
{	
	public function RenderBreadcrumbs($separator = ' > ', $siteRoot = 'Home')
	{
		$parents = $this->view->pageObj->getParents();
		if(is_array($parents) && count($parents) > 0)
		{
		    $path = null;
			foreach ($parents as $parent) {
			    $label = $this->view->pageObj->getLabel($parent);
				$link = '/' .  ToolboxString ::addHyphens($label);
				$path .= $link;
				$arrLinks[] = "<a href='{$path}' class='breadcrumb'>{$parent->title}</a>";
			}
		}
		$arrLinks[] = "<a href='' class='breadcrumb last'>{$this->view->page->title}</a>";

		return implode($separator, $arrLinks);
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
