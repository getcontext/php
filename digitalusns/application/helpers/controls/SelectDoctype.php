<?php

namespace DSF\View\Helper\Controls;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * SelectDoctype helper
 *
 * @uses viewHelper DSF_View_Helper_Controls
 */


use Zend\View\ViewInterface as ViewInterface;




class  SelectDoctype  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function selectDoctype($name, $value, $attr = null) {
        $data = array(
            'XHTML1_TRANSITIONAL' => 'XHTML1_TRANSITIONAL',
            'XHTML11' => 'XHTML11',
            'XHTML1_STRICT' => 'XHTML1_STRICT',
            'XHTML1_FRAMESET' => 'XHTML1_FRAMESET',
            'XHTML_BASIC1' => 'XHTML_BASIC1',
            'HTML4_STRICT' => 'HTML4_STRICT',
            'HTML4_LOOSE' => 'HTML4_LOOSE',
            'HTML4_FRAMESET' => 'HTML4_FRAMESET'
        );

        return $this->view->formSelect($name, $value, $attr, $data);
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
