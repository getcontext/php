<?php

namespace DSF\View\Helper\Form;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;
use Zend\Date as Date;




class  DatePicker 
{

	/**
	 * this helper renders a date picker (requires jquery date)
	 * 
	 * @param string $name
	 * @param timestamp $value
	 * 
	 */
	public function DatePicker($name, $value = null){
	    //format the timestamp
	    
	    if($value > 0)
	    {
    	     Date ::setOptions(array('format_type' => 'php'));
    	    $date = new  Date ($value);
    		$value = $date->toString('m-d-Y');
	    }else{
	        //we dont want any value that is not a valid date
	        $value = null;
	    }
			
		return $this->view->formText($name, $value, array('class' => 'date-picker'));
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
