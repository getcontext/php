<?php

namespace DSF\View\Helper\Filesystem;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class \DSF_View_Helper_Filesystem_RenderFileBrowser
{	
	public function RenderFileBrowser($parentId, $link, $basePath = null, $level = 0, $id = 'fileTree')
	{
		$links = array();
		$tree = new \Page();

		$children = $tree->getChildren($parentId);
		
		foreach ($children as $child)
		{			
			if($tree->hasChildren($child))
			{
				$newLevel = $level + 1;
				$submenu = $this->view->RenderFileBrowser($child->id, $link, $basePath, $newLevel);
				$icon = 'folder.png';
			}else{
			    $icon = "page_white_text.png";
				$submenu = false;
			}
			
			$links[] ="<li class='menuItem'>" . $this->view->link($child->name, $link . $child->id, $icon) . $submenu . '</li>';
		}
		
		if(is_array($links))
		{
			if($level == 0){
				$strId = "id='{$id}'";
			}else{
			    $strId = null;
			}
			$filetree = "<ul {$strId}>" . implode(null, $links) . "</ul>";
			return  $filetree;
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
