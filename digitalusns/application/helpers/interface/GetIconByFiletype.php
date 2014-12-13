<?php
/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * GetIconByType helper
 *
 * @uses viewHelper DSF_View_Helper_Interface
 */
class \DSF_View_Helper_Interface_GetIconByFileType {
    
    /**
     * @var \Zend\View\ViewInterface 
     */
    public $view;
    public $defaultIcon = 'page.png';
    public $folderIcon = 'folder.png';
    public $icons = array();
    /**
     *  
     */
    public function getIconByFileType($file, $asImage = true) {
	    $config = \Zend\Registry::get('config');
	    $this->icons = $config->filetypes; 
	    $icon = $this->getIcon($file);
	    if($asImage) {
	        $base = $this->view->baseUrl . '/' . $config->filepath->icons; 
	        return "<img src='{$base}/{$icon}' />";
	    }else{
	        return $icon;
	    }
    }
	
	public function getIcon($file)
	{
        $filetype = \DSF\Media\Filetype::load($file);
        $type = $filetype->key;
        
	    if(isset($this->icons->$type)) {
	        $filetype = $this->icons->$type;
	        return $filetype->icon;
	    }else{
	        return $this->defaultIcon;
	    }
    }
    
    /**
     * Sets the view field 
     * @param $view \Zend\View\ViewInterface
     */
    public function setView(\Zend\View\ViewInterface $view) {
        $this->view = $view;
    }
}
