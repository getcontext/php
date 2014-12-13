<?php

namespace DSF\View\Helper\Interface;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * Link helper
 *
 * @uses viewHelper DSF_View_Helper_Interface
 */


use Zend\View\ViewInterface as ViewInterface;
use Zend\Registry as Registry;




class  Link  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    public $iconPath;
    
    /**
     *  
     */
    public function link($label, $link, $icon = null, $class='link') {
        $config =  Registry ::get('config');
        $this->iconPath = $config->filepath->icons;
        $linkParts[] = "<a href='{$link}' class='{$class}'>";
        $iconPath = $this->view->baseUrl . $this->iconPath;
        if(null !== $icon) {
            $linkParts[] = "<img src='{$this->view->baseUrl}/{$iconPath}/{$icon}' alt='({$label}) ' class='icon' />";
        }
        if(!empty($label)) {
            $linkParts[] = $this->view->GetTranslation((string)$label);
        }
        $linkParts[] = "</a>";
        return implode(null, $linkParts);
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
