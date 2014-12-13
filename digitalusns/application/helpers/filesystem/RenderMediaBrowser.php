<?php

namespace DSF\View\Helper\Filesystem;




use DSF\Filesystem\File as File;
use Zend\view\viewInterface as viewInterface;
use DSF\Filesystem\Dir as Dir;
use DSF\Toolbox\ToolboxString as ToolboxString;
use view\viewInterface as viewInterface;




class  RenderMediaBrowser 
{	

    
	public function RenderMediaBrowser($path, $folderLink, $fileLink)
	{
  
		$folders =  Dir ::getDirectories('./' . $path);
		$files =  File ::getFilesByType('./' . $path,false,false,true);
		$links = null;
		
		if(is_array($folders) && count($folders) > 0) {
    		foreach ($folders as $folder)
    		{	
    		    $folderPath = $path . '/' . $folder;
    		    $link =  ToolboxString ::addUnderscores($folderPath);
    		    //remove reference to media
    		    $link = str_replace('media_', '', $link);
    		    $submenu = $this->view->RenderMediaBrowser($folderPath, $folderLink, $fileLink);
    		    $links[] = "<li class='menuItem'>" . $this->view->link($folder, '/' . $folderLink . '/' . $link, 'folder.png') . $submenu . '</li>';
    		}
		}
		
		if(is_array($files) && count($files) > 0) {
    		foreach ($files as $file) {
    		    if(substr($file,0,1) != '.') {
    		        $filePath = $path . '/' . $file;
    			    $links[] ="<li class='menuItem'>" . 
    			    $this->view->link($file , $this->view->baseUrl . $fileLink . '/' . $filePath, $this->view->getIconByFiletype($file, false)) . "</li>";
    		    }
		    }
		}
		
		if(is_array($links))
		{
			$filetree = "<ul id='fileTree' class='treeview'>" . implode(null, $links) . "</ul>";
			return  $filetree;
		}
		return null;
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
