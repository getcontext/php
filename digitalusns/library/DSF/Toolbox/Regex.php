<?php

namespace DSF\Toolbox;

 

/**
 * DSF CMS
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://digitalus-media.com/license/new-bsd
 * If you did not receive a copy \of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@digitalus-media.com so we can send you a copy immediately.
 *
 * @category   DSF CMS
 * @package   DSF_Core_Library
 * @copyright  Copyright (c) 2007 - 2008,  Digitalus Media USA (digitalus-media.com)
 * @license    http://digitalus-media.com/license/new-bsd     New BSD License
 * @version    $Id: Regex.php Tue Dec 25 21:13:52 EST 2007 21:13:52 forrest lyman $
 */







class  Regex 
{
	/**
	 * removes the trailing slash
	 *
	 * @param string $string
	 * @return string
	 */
	static function stripTrailingSlash($string)
	{
		return preg_replace("/\/$/", '', $string);
	}
	
	/**
	 * strips the file extension
	 *
	 * @param string $string
	 * @return string
	 */
	static function stripFileExtension($string)
	{
		return preg_replace("/\..*$/", '', $string);
	}
	
	/**
	 * returns the html between the the body tags
	 * if filter is set then it will return the html between the specified tags
	 *
	 * @param string $html
	 * @param string $filter
	 * @return string
	 */
	static function extractHtmlPart($html, $filter = false)
	{
		if($filter)
		{
			$startTag = "<{$filter}>";
			$endTag = "</{$filter}>";
		}else{
			$startTag = "<body>";
			$endTag = "</body>";
		}
		$startPattern = ".*" . $startTag;
		$endPattern = $endTag . ".*";
		
		$noheader = eregi_replace($startPattern, "", $html);
		
		$cleanPart = eregi_replace($endPattern, "", $noheader);
		
		return $cleanPart;
	}
	
	/**
	 * replaces multiple spaces with a single space
	 *
	 * @param string $string
	 * @return string
	 */
	static function stripMultipleSpaces($string)
	{
	    return trim(preg_replace('/\s+/', ' ',$string));
	}
	
	/**
	 * note that this does not transfer any \of the attributes
	 *
	 * @param string $tag
	 * @param string $replacement
	 * @param string $content
	 */
	static function replaceTag($tag, $replacement, $content, $attributes = null)
	{
	    $content = preg_replace("/<{$tag}.*?>/", "<{$replacement} {$attributes}>", $content);
	    $content = preg_replace("/<\/{$tag}>/", "</{$replacement}>", $content);	
	    return $content;
	}
	
}
