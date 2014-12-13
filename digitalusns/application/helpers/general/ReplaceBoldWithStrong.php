<?php

namespace DSF\View\Helper\General;




use DSF\Toolbox\Regex as Regex;




class  ReplaceBoldWithStrong 
{

	public function ReplaceBoldWithStrong($content, $strongClass = null)
	{
	    if($strongClass){
	        $class = "class='{$strongClass}'";
	    }
	    
	    //get the content body
	    $content =  Regex ::extractHtmlPart($content, 'body');
	    
	    //replace the tags
	    $content =  Regex ::replaceTag('b', 'strong', $content, $class) ;
        return $content;
	}
}
