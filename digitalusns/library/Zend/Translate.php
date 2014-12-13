<?php

namespace Zend;


/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy \of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package     Translate 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  Loader 
 */
require_once 'Zend/Loader.php';


/**
 * @category   Zend
 * @package     Translate 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Translate\Exception as TranslateException;
use Zend\Translate\Adapter as Adapter;
use Zend\Cache\Core as Core;
use Zend\Loader as Loader;
use Zend\Locale as Locale;




class  Translate  {
    /**
     * Adapter names constants
     */
    const AN_ARRAY   = 'array';
    const AN_CSV     = 'csv';
    const AN_GETTEXT = 'gettext';
    const AN_INI     = 'ini';
    const AN_QT      = 'qt';
    const AN_TBX     = 'tbx';
    const AN_TMX     = 'tmx';
    const AN_XLIFF   = 'xliff';
    const AN_XMLTM   = 'xmltm';

    const LOCALE_DIRECTORY = 1;
    const LOCALE_FILENAME  = 2;

    /**
     * Adapter
     *
     * @var  Adapter 
     */
    private $_adapter;
    private static $_cache = null;


    /**
     * Generates the standard translation object
     *
     * @param  string              $adapter  Adapter to use
     * @param  array               $data     Translation source data \for the adapter
     *                                       Depends on the Adapter
     * @param  string| Locale   $locale   OPTIONAL locale to use
     * @param  array               $options  OPTIONAL options \for the adapter
     * @throws  TranslateException 
     */
    public function __construct($adapter, $data, $locale = null, array $options = array())
    {
        $this->setAdapter($adapter, $data, $locale, $options);
    }


    /**
     * Sets a new adapter
     *
     * @param  string              $adapter  Adapter to use
     * @param  string|array        $data     Translation data
     * @param  string| Locale   $locale   OPTIONAL locale to use
     * @param  array               $options  OPTIONAL Options to use
     * @throws  TranslateException 
     */
    public function setAdapter($adapter, $data, $locale = null, array $options = array())
    {
        switch (strtolower($adapter)) {
            case 'array':
                $adapter = '\Zend\Translate\Adapter\AdapterArray';
                break;
            case 'csv':
                $adapter = '\Zend\Translate\Adapter\Csv';
                break;
            case 'gettext':
                $adapter = '\Zend\Translate\Adapter\Gettext';
                break;
            case 'ini':
                $adapter = '\Zend\Translate\Adapter\Ini';
                break;
            case 'qt':
                $adapter = '\Zend\Translate\Adapter\Qt';
                break;
            case 'tbx':
                $adapter = '\Zend\Translate\Adapter\Tbx';
                break;
            case 'tmx':
                $adapter = '\Zend\Translate\Adapter\Tmx';
                break;
            case 'xliff':
                $adapter = '\Zend\Translate\Adapter\Xliff';
                break;
            case 'xmltm':
                $adapter = '\Zend\Translate\Adapter\XmlTm';
                break;
        }

         Loader ::loadClass($adapter);
        if (self::$_cache !== null) {
            call_user_func(array($adapter, 'setCache'), self::$_cache);
        }
        $this->_adapter = new $adapter($data, $locale, $options);
        if (!$this->_adapter instanceof  Adapter ) {
            require_once 'Zend/Translate/Exception.php';
            throw new  TranslateException ("Adapter " . $adapter . " does not extend  Translate _Adapter'");
        }
    }


    /**
     * Returns the adapters name and it's options
     *
     * @return  Adapter 
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Sets a cache \for all instances \of  Translate 
     *
     * @param   Core  $cache Cache to store to
     * @return void
     */
    public static function setCache( Core  $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Returns the set cache
     *
     * @return  Core  The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Calls all methods from the adapter
     */
    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method)) {
            return call_user_func_array(array($this->_adapter, $method), $options);
        }
        require_once 'Zend/Translate/Exception.php';
        throw new  TranslateException ("Unknown method '" . $method . "' called!");
    }
}
