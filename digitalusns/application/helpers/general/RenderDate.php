<?php

namespace DSF\View\Helper\General;




use Zend\Date as Date;




class  RenderDate 
{

	/**
	 * defaults to current date
	 * we use php's formating here
	 */
	public function RenderDate($timestamp = null, $format = 'F j, Y'){
	    if($timestamp == null){
	        $timestamp = time();
	    }
	     Date ::setOptions(array('format_type' => 'php'));
	    $date = new  Date ($timestamp);
        return $date->toString($format);
	}
}
