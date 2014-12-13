<?php

namespace DSF\View\Helper\Admin;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderText 
{
  
    /**
     *
     * @return unknown
     */
	public function RenderText($key, $tag = null)
	{
	    $xhtml = null;
	    if($tag != null){
	        $xhtml .= "<{$tag}>";
	    }
	    $xhtml .= $this->view->GetTranslation($key);
	    if($tag != null){
	        $xhtml .= "</{$tag}>";
	    }
	    return $xhtml;
	    
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
