<?php

namespace DSF\View\Helper\Admin;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * CheckSkinStyles helper
 *
 * @uses viewHelper DSF_View_Helper_Admin
 */


use DSF\Filesystem\File as File;
use Zend\View\ViewInterface as ViewInterface;
use DSF\Filesystem\Dir as Dir;
use Zend\Acl\Exception as AclException;
use Zend\Registry as Registry;




class  CheckSkinStyles  {
	
	/**
	 * @var  ViewInterface  
	 */
	public $view;
	public $partialFile = 'design/listSkin.phtml';
	
	/**
	 *  
	 */
	public function checkSkinStyles($name, $values) {
		$config =  Registry ::get("config");
		$basePath = $config->design->pathToSkins;
		$xhtml = array();
		$this->view->name = $name;
		$this->view->selectedStyles = $values;
				
		//load the skin folders
		if(is_dir('./' . $basePath)) {
			$folders =  Dir ::getDirectories('./' . $basePath);
			if(count($folders) > 0) {
				foreach ($folders as $folder) {
					$this->view->skin = $folder;
					$styles =  File ::getFilesByType('./' . $basePath . '/' . $folder . '/styles', 'css');
					if(is_array($styles)) {
						foreach ($styles  as $style) {
							//add each style sheet to the hash
							// key = path / value = filename
							$hashStyles[$style] = $style;
						}
						$this->view->styles = $hashStyles;
						$xhtml[] = $this->view->render($this->partialFile);
						unset($hashStyles);
					}
				}				
			}
		}else{
			throw new  AclException ("Unable to locate skin folder");
		}
		
		return implode(null,$xhtml);
		
	}
	
	/**
	 * Sets the view field 
	 * @param $view  ViewInterface 
	 */
	public function setView( ViewInterface  $view) {
		$this->view = $view;
	}
}
