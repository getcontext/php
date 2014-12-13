<?php

namespace DSF\Menu;




use Zend\Controller\Front as Front;
use DSF\Toolbox\ToolboxString as ToolboxString;
use Zend\Db\Table\Row as Row;
use DSF\Toolbox\Page as ToolboxPage;
use DSF\Menu as DSFMenu;





class  Item  {
    protected $_innerItem;
    public $id;
    public $label;
    public $link;
    public $visible;
    public $hasSubmenu = false;
    
    function __construct( Row  $item) {
        $this->_innerItem = $item;
        $this->label =  ToolboxPage ::getLabel($item);
        $this->link =  ToolboxPage ::getUrl($item);
        
        if($item->show_on_menu) {
            $this->visible = true;
        }else{
            $this->visible = false;
        }
        
        $page = new \Page();
        if($page->hasChildren($item)) {
            $this->hasSubmenu = true;
        }else{
            $this->hasSubmenu = false;
        }
    }
    
    public function getSubmenu()
    {
        return new  DSFMenu ($this->_innerItem->id);
    }
    
    public function asHyperlink($id = null, $class = null)
    {
        $cleanLink =  ToolboxString ::addHyphens($this->link);
        $front =  Front ::getInstance();
        $baseUrl = $front->getBaseUrl();
        return "<a href='" . $baseUrl . "/{$cleanLink}' id='{$id}' class='{$class}'>$this->label</a>";
    }
}

?>
