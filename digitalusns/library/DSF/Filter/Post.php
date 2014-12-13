<?php

namespace DSF\Filter;

 

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
 * @version    $Id: Post.php Tue Dec 25 20:51:45 EST 2007 20:51:45 forrest lyman $
 */


use Zend\Filter\StripTags as StripTags;
use Zend\Filter\Alpha as Alpha;
use Zend\Debug as Debug;
use Zend\Date as Date;





class  Post  
{
	/**
	 * default method, strips tags
	 *
	 * @param string $key
	 */
	static function get($key)
	{
		$filter = new  StripTags ();
		$post = self::toObject();
		return trim($filter->filter($post->$key));
		
	}
	
	/**
	 * set a post value
	 *
	 * @param string $key
	 * @param string $value
	 */
	static function set($key, $value)
	{
		$_POST[$key] = $value;
	}
	
	/**
	 * returns the whole post array as an object
	 *
	 * @todo make this method handle array data
	 * @return \stdClass object
	 */
	static function toObject()
	{
		$post = new \stdClass();
		foreach ($_POST as $k => $v)
		{
			if(is_array($v)) {
				$post->$k = $v;
			}else{
				$post->$k = stripslashes($v);
			}
		}
		return $post;
	}
	
	/**
	 * test whether the key is set
	 *
	 * @param string $key
	 * @return bool
	 */
	static function has($key){
		if(isset($_POST[$key])){return true;}	
	}
	
	/**
	 * returns the value without any filters
	 *
	 * @param string $key
	 * @return mixed
	 */
	static function raw($key)
	{
		return $_POST[$key];
	}
	
	/**
	 * filters the value as alpha
	 *
	 * @param string $key
	 * @return string
	 */
	static function alpha($key)
	{
		$filter = new  Alpha ();
		$post =self::toObject();
		return trim($filter->filter($post->$key));
	}
	
	/**
	 * filters the value as an integer
	 *
	 * @param string $key
	 * @return int
	 */
	static function int($key)
	{
		$post = self::toObject();
		return intval($post->$key);
	}

	/**
	 * strips and adds slashes. i strip them first because the html editor adds them as well
	 *
	 * @param string $key
	 * @return string
	 */
	static function text($key)
    {
        //you must strip slashes first, as the HTML editors add them
        //by doing this you are able to process both raw HTML and WYSIWYG HTML
		$post = self::toObject();
        return trim(addslashes($post->$key));
    }

    /**
     * returns the value as a floating point #
     *
     * @param string $key
     * @return float
     */
    static function float($key)
    {
		$post = self::toObject();
		if(is_float($post[$key])){
        	return floatval($post->$key);
		}
    }

    /**
     * returns the value as a timestamp
     * the value is evaluated with zend_date
     *
     * @param string $key
     * @return timestamp
     */
    static function date($key)
    {
		$post =self::toObject();
		if($post[$key]){
			$date = new  Date ($post->$key);
			return $date->get( Date ::TIMESTAMP);
		}
    }
    
    /**
     * dumps the post variables
     *
     */
    static function dump()
    {
	     Debug ::dump($_POST);
    }

}
