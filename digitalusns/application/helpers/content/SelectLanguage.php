<?php

namespace DSF\View\Helper\Content;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * SelectLanguage helper
 *
 * @uses viewHelper DSF_View_Helper_Content
 */


use Zend\View\ViewInterface as ViewInterface;
use Zend\Registry as Registry;




class  SelectLanguage  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function selectLanguage($name, $value, $attribs = null) {
        //select version
		$config =  Registry ::get('config');
		$siteVersions = $config->language->translations;
		
		foreach ($siteVersions as $locale => $label) {
		    $data[$locale] = $label;
		}
		
		return $this->view->formSelect($name, $value, $attribs, $data);
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
