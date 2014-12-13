<?php

namespace DSF\Data;

 

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
 * @version    $Id: Storage.php Tue Dec 25 20:33:46 EST 2007 20:33:46 forrest lyman $
 */


use Zend\Session\SessionNamespace as SessionNamespace;
use Zend\Session as Session;





class  Storage 
{
	/**
	 * the storage object \for the data
	 *
	 * @var zend_session_namespace
	 */
	protected $_storage;
	
	/**
	 * set the storage
	 *
	 */
	function __construct()
	{
		$this->_storage = new  SessionNamespace ('dataStorage');
	}
	
	/**
	 * set the data
	 *
	 * @param array $data
	 */
	function set($data)
	{
		if(is_array($data)){
			foreach ($data as $k => $v){
				$array[$k] = $v; //make sure we dont try to store an object in the session
			}
			$this->_storage->data = $array;
		}
	}
	
	/**
	 * save the current post array
	 *
	 */
	function savePost()
	{
		$this->set($_POST);
	}
	
	/**
	 * returns the saved data
	 * if persist false this deletes the data from the storage
	 * 
	 * @param bool $persist
	 * @return array
	 */
	function get($persist = false)
	{
		if(!empty($this->_storage->data))
		{
			$data = new \stdClass();
			foreach ($this->_storage->data as $k => $v)
			{
				$data->$k = $v;
			}
			if(!$persist)
			{
				 Session ::namespaceUnset('dataStorage');
			}
			return $data;
		}
	}
}
