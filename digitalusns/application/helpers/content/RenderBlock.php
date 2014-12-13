<?php

namespace DSF\View\Helper\Content;




use zend\controller\front as front;
use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderBlock 
{
	public function RenderBlock($module, $block, $params = null){
	    $front =  front ::getInstance();
	    $controllers = $front->getControllerDirectory();
	    if(isset($controllers['mod_' . $module])) {
	        //create a new view instance (that shares the other views params)
	        $view = clone ($this->view);

	        $path = str_replace('controllers', 'blocks', $controllers['mod_' . $module]) . '/' . $block;
	        
	        
	        //require the controller class
	        require_once($path . '/controller.php');
	        
	        //create an instance
	        $className = ucwords($module) . '_Block_' . ucwords($block);

	        $block = new $className($view, $params);
	        
	        //render the view
	        $view->addScriptPath($path);
	        return $view->render('view.phtml');
	    }
	}
		
    /**
     * Set this->view object
     *
     * @param  Zend_this-> viewInterface  $this->view
     * @return Zend_this->view_Helper_DeclareVars
     */
    public function setview( viewInterface  $view)
    {
        $this->view = $view;
        return $this;
    }
}
