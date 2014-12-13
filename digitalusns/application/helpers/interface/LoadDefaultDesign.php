<?php

namespace DSF\View\Helper\Interface;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * LoadDefaultDesign helper
 *
 * @uses viewHelper DSF_View_Helper_Interface
 */


use Zend\View\ViewInterface as ViewInterface;




class  LoadDefaultDesign  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function loadDefaultDesign() {
        $mdlDesign = new \Design();
        $design = $mdlDesign->getDefaultDesign();
        $mdlDesign->setDesign($design->id);
        
        //todo: this is duplicated in the builder
        
		//the design model returns the stylesheets organized by skin
		$skins = $mdlDesign->getStylesheets();
		if(is_array($skins)) {
			foreach ($skins as $skin => $styles) {
				if(is_array($styles)) {
					foreach ($styles as $style) {
						$this->view->headLink()->appendStylesheet('/skins/' . $skin . '/styles/' . $style);	
					}
				}		
			}
		}
		
		$this->view->layout = $mdlDesign->getLayout();
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
