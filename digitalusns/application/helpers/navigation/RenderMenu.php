<?php

namespace DSF\View\Helper\Navigation;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;
use DSF\Menu as Menu;




class  RenderMenu 
{
    public $levels = 1;
    public $currentLevel = 1;
    
	public function RenderMenu($parentId = 0, $levels = null, $currentLevel = null, $id = "menu")
	{
	    if(null !== $levels) {
	        $this->levels = $levels;
	    }
	    
	    if(null !== $currentLevel) {
	        $this->currentLevel = $currentLevel;
	    }
	    
	    $menu = new  Menu ($parentId);
	    $links = array();	
	    
		if(count($menu->items) > 0) {
		    foreach ($menu->items as $item) {
		        $link = "<li id='menu_item_{$item->id}' class='menuItem'>" . $item->asHyperlink();
                //check to see if we should render a submenu
                if(($levels > $currentLevel) && ($item->hasSubmenu)) {
                    $newLevel = $currentLevel + 1;
                    $link .= $this->view->RenderMenu($item->id, $levels, $newLevel, 'submenu_' . $item->id);
                }
                $link .=  "</li>";
                $links[] = $link;
		    }
		}
		
		if(count($links) > 0)
		{
			return  "<ul id='{$id}'>" . implode(null, $links) . "</ul>";
		}else{
		    return null;
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
