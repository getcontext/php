<?php

namespace DSF\View\Helper\Controls;




use DSF\Filesystem\File as File;
use Zend\view\viewInterface as viewInterface;
use DSF\Toolbox\Regex as Regex;
use view\viewInterface as viewInterface;




class  SelectModulePage 
{	
	public function SelectModulePage($name, $module, $value, $attribs = null)
	{
		$pages =  File ::getFilesByType('./application/modules/' . $module . '/views/scripts/public', 'phtml');
		if(is_array($pages))
		{
		    $data[] = "Select One";
    		foreach ($pages as $page)
    		{
    		    $page =  Regex ::stripFileExtension($page);
    		    $data[$page] = $page;
    		}
    		return $this->view->formSelect($name, $value, $attribs, $data);
		}else{
		    return "There are no pages in this module";
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
