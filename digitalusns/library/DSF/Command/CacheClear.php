<?php

namespace DSF\Command;

 

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
 * @version    $Id: ImportSitemap.php Tue Dec 25 19:57:20 EST 2007 19:57:20 forrest lyman $
 */


use DSF\Command\CommandAbstract as CommandAbstract;
use Zend\Registry as Registry;
use Zend\Cache as Cache;





class  CacheClear  extends  CommandAbstract  
{
    
    /**
     *
     */
    function __construct()
    {
    }
    
    /**
     *clears the cache
     * if the param key is set it will only clear the specified one
     */
    function run($params)
    { 
    	if( Registry ::isRegistered('cache')) {
	    	$cache =  Registry ::get('cache');
	        if(isset($params['key'])){
	            $cache->clean($params['key']);
	            $this->log("Cache cleared Key = " . $params['key']);
	        }else{
	            $cache->clean( Cache ::CLEANING_MODE_ALL);
	            $this->log("Cache cleared");
	        }
    	}else{
    		$this->log("Error: Cache is not registered");
    	}
        
    }
    
    /**
     * returns details about the current command
     *
     */
    function info()
    {
        $this->log("The cache clear function will either clear a specified key or all cache files if a key is not specified.");
        $this->log("Params: key (string, optional)");
    }
}
