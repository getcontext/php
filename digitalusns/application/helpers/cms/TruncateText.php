<?php

namespace DSF\View\Helper\Cms;




use Zend\Filter\StripTags as StripTags;
use unknown\type as type;




class  TruncateText 
{
	/**
	 * returns a truncated version \of the text
	 *
	 * @param  type  $text
	 * @param  type  $count
	 * @return unknown
	 */
	function truncateText($text, $count = 25, $stripTags = true)
	{
		if($stripTags){
		    $filter = new  StripTags ();
    		$text = $filter->filter($text);
		}
		$words=split(" ",$text); 
		$text = (string)join(" ",array_slice($words,0,$count));
        return $text;
	}
}
