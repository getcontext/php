<?php

namespace Zend\Service;


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
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * @see  Loader 
 */
require_once 'Zend/Loader.php';

/**
 * @see  Client 
 */
require_once 'Zend/Http/Client.php';

/**
 * This class allows Nirvanix authentication credentials to be specified
 * in one place and provides a factory \for returning convenience wrappers
 * around the Nirvanix web service namespaces.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Http\Client as Client;
use Zend\Loader as Loader;




class  Nirvanix 
{
    /**
     * Options to pass to namespace proxies
     * @param array
     */
    protected $_options;

    /**
     * Class constructor.  Authenticates with Nirvanix to receive a 
     * sessionToken, which is then passed to each future request.
     *
     * @param  array  $authParams  Authentication POST parameters.  This
     *                             should have keys "username", "password",
     *                             and "appKey".
     * @param  array  $options     Options to pass to namespace proxies
     */
    public function __construct($authParams, $options = array())
    {
        // merge options with default options
        $defaultOptions = array('defaults'   => array(),
                                'httpClient' => new  Client (),
                                'host'       => 'http://services.nirvanix.com');
        $this->_options = array_merge($defaultOptions, $options);

        // login and save sessionToken to default POST params
        $resp = $this->getService('Authentication')->login($authParams);
        $this->_options['defaults']['sessionToken'] = (string)$resp->SessionToken;
    }    

    /**
     * Nirvanix divides its service into namespaces, with each namespace
     * providing different functionality.  This is a factory method that
     * returns a preconfigured  Nirvanix _Namespace_Base proxy.
     *
     * @param  string  $namespace  Name \of the namespace
     * @return  Nirvanix _Namespace_Base
     */
    public function getService($namespace, $options = array())
    {
        switch ($namespace) {
            case 'IMFS':
                $class = '\Zend\Service\Nirvanix\Namespace\Imfs';
                break;
            default:
                $class = '\Zend\Service\Nirvanix\Namespace\Base';
        }

        $options['namespace'] = ucfirst($namespace);
        $options = array_merge($this->_options, $options);

         Loader ::loadClass($class);
        return new $class($options);
    }
    
    /**
     * Get the configured options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

}
