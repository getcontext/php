<?php

namespace DSF\View\Helper\Content;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * RenderTextile helper
 *
 * @uses viewHelper DSF_View_Helper_Content
 */


use DSF\Content\Render\Textile as Textile;
use Zend\View\ViewInterface as ViewInterface;




class  RenderTextile  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function renderTextile($content) {
        $content = stripslashes($content);
        $textile = new  Textile ();
        return $textile->TextileThis($content);
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
