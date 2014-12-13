<?php

namespace DSF\View\Helper\Admin;




use Zend\Controller\Front as Front;
use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;
use unknown\type as type;




class  RenderOptions 
{
    public $optionsPath;
    public $defaultHeadline = "Options";
  
    /**
     * this helper renders the admin options.
     * 
     * you can add content before the body by setting options_before placeholder
     * you can add content after the body by setting options_after placeholder
     *
     * @param  type  $selectedItem
     * @param  type  $id
     * @return unknown
     */
	public function RenderOptions($id = 'Options')
	{
        $this->setOptionsPath();
        
        //render the column first so you can set the headline pla
        $column = $this->renderBody();
        $headline = $this->renderHeadline();
   
        return $headline . $column;
	}
	
	public function renderHeadline()
	{
	    return "<h2 class='top'>" . $this->view->placeholder('optionsHeadline') . "</h2>";
	}
	
	public function renderBody()
	{
	    $xhtml = "<div class='columnBody'>";
	    
	    //you can add content before the body by setting options_before placeholder
        $xhtml .= $this->view->placeholder('options_before');
	    
	    $xhtml .= $this->view->render($this->optionsPath);

	    //you can add content after the body by setting options_after placeholder
        $xhtml .= $this->view->placeholder('options_after');
	    
        $xhtml .= "</div>";
	    return $xhtml;
	}
	
	public function setOptionsPath()
	{
	    $front =  Front ::getInstance();
	    $request = $front->getRequest();
	    $controller = $request->getControllerName();
	    $action = $request->getActionName();

        $this->optionsPath = $controller . '/' . $action . '.options.phtml';
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
