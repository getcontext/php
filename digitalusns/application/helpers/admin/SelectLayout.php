<?php

namespace DSF\View\Helper\Admin;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * SelectLayout helper
 *
 * @uses viewHelper DSF_View_Helper_Admin
 */


use DSF\Filesystem\File as File;
use Zend\View\ViewInterface as ViewInterface;
use Zend\Registry as Registry;




class  SelectLayout  {
	
	/**
	 * @var  ViewInterface  
	 */
	public $view;
	
	/**
	 *  
	 */
	public function selectLayout($name, $value = null, $attr = null, $defaut = null) {
		$config =  Registry ::get("config");
		$pathToLayouts = $config->design->pathToLayouts;
		$layouts =  File ::getFilesByType($pathToLayouts, 'phtml');
		
		if($defaut == NULL) {$defaut = $this->view->GetTranslation('Select One');}
		$options[0] = $defaut;
		
		if(is_array($layouts)) {
			foreach ($layouts as $layout)
			{
				$options[$layout] = $layout;
			}
			return $this->view->formSelect($name, $value, $attr, $options);
		}else{
			return null;
		}
		
	}
	
	/**
	 * Sets the view field 
	 * @param $view  ViewInterface 
	 */
	public function setView( ViewInterface  $view) {
		$this->view = $view;
	}
}
