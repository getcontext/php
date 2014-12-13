<?php

namespace DSF\View\Helper\Content;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * RenderContentTemplate helper
 *
 * @uses viewHelper DSF_View_Helper_Content
 */


use DSF\Content\Template\Loader as Loader;
use Zend\View\ViewInterface as ViewInterface;




class  RenderContentTemplate  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function renderContentTemplate($template, $content) {
        $loader = new  Loader ();
        $template = $loader->load($template);
        return $template->render($content);
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
