<?php

namespace DSF\View\Helper\General;




use Zend\view\viewInterface as viewInterface;
use DSF\View\Message as Message;
use view\viewInterface as viewInterface;
use DSF\View\Error as Error;




class  RenderAlert 
{

	/**
	 * comments
	 */
	public function RenderAlert(){
        $m = new  Message ();
        $ve = new  Error ();
        $alert = false;
        $message = null;
        $verror = null;
        
        $alert = null;

        if($ve->hasErrors()){
            $verror = "<p>The following errors have occurred:</p>" . $this->view->HtmlList($ve->get());
            $alert .= "<fieldset><legend>Errors</legend>" . $verror . "</fieldset>";
        }
        
        if($m->hasMessage()){
            $message .= "<p>" . $m->get() . "</p>";
            $alert .= "<fieldset><legend>Message</legend>" . $message . "</fieldset>";
        }
        
        //after this renders it clears the errors and messages
        $m->clear();
        $ve->clear();
        
		return $alert;
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
