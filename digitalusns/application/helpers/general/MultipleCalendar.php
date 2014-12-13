<?php

namespace DSF\View\Helper\General;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  MultipleCalendar 
{	
    /**
     * renders a set \of calendars with links to each day
     * pass this an array \of the months with the selected days
     * @param $months = array(numericYear-numericMonth = array(
     *                          numericDay => array('link', 'class', 'content to render on day')
     *                          ));
     *
     */
	public function MultipleCalendar($months = array())
	{
	    $xhtml = null;
        foreach ($months as $month => $selectedDays){
            $monthParts = explode('-', $month);
            if(!is_array($selectedDays)){
                $selectedDays = array();
            }
            $xhtml .= $this->view->Calendar($monthParts[0], $monthParts[1], $selectedDays);
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
