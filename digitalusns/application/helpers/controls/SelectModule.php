<?php

namespace DSF\View\Helper\Controls;




use DSF\Filesystem\File as File;
use Zend\view\viewInterface as viewInterface;
use DSF\Filesystem\Dir as Dir;
use DSF\Toolbox\Regex as Regex;
use view\viewInterface as viewInterface;




class  SelectModule 
{	
	public function SelectModule($name, $value, $attribs = null)
	{
		$modules =  Dir ::getDirectories('./application/modules');
		if(is_array($modules))
		{
		    $data[] = $this->view->GetTranslation("Select a module");
    		foreach ($modules as $module)
    		{
    		    $pages =  File ::getFilesByType('./application/modules/' . $module . '/views/scripts/public', 'phtml');
		        if(is_array($pages)) {
		            foreach ($pages as $page) {
		                $page =  Regex ::stripFileExtension($page);
		                $data[$module . '_' . $page] = $module . ' -> ' . $page;
		            }
		        }
    		}
    		return $this->view->formSelect($name, $value, $attribs, $data);
		}else{
		    return "There are no modules currently installed";
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
