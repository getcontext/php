<?php

namespace DSF\View\Helper\Content;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * RenderNode helper
 *
 * @uses viewHelper DSF_View_Helper_Content
 */


use Zend\View\ViewInterface as ViewInterface;




class  RenderNode  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function renderNode($uri, $node) {
        $page = new \Page();
        $content = $page->getContent($uri);
        return $content[$node];
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
