<?php

namespace DSF\View\Helper\Form;








class  ModuleAction 
{

	/**
	 * comments
	 */
	public function ModuleAction($stripParams = false){
	    $uri = $_SERVER['REQUEST_URI'];
		if($stripParams && strpos($uri, '/p/')){
		    $parts = explode('/p/', $uri);
		    $uri = $parts[0];		    
		}
		return $uri;
	}
}
