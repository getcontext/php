<?php

namespace Zend\Wildfire\Channel;


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
 * @package    Zend_Wildfire
 * @subpackage Channel
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  WildfireChannelInterface  */
require_once 'Zend/Wildfire/Channel/Interface.php';

/**  WildfireException  */
require_once 'Zend/Wildfire/Exception.php';

/** \Zend\Controller\Request\RequestAbstract */
require_once('Zend/Controller/Request/Abstract.php');

/** \Zend\Controller\Response\ResponseAbstract */
require_once('Zend/Controller/Response/Abstract.php');

/** \Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**  JsonStream  */
require_once 'Zend/Wildfire/Protocol/JsonStream.php';

/**  Front  **/
require_once 'Zend/Controller/Front.php';

/**
 * Implements communication via HTTP request and response headers \for Wildfire Protocols.
 * 
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Channel
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Wildfire\Protocol\JsonStream as JsonStream;
use Zend\Wildfire\Channel\ChannelInterface as WildfireChannelInterface;
use Zend\Wildfire\Exception as WildfireException;
use Zend\Controller\Front as Front;
use Zend\Loader as Loader;




class  HttpHeaders  extends \Zend_Controller_Plugin_Abstract implements  WildfireChannelInterface 
{
    /**
     * The string to be used to prefix the headers.
     * @var string
     */
    protected static $_headerPrefix = 'X-WF-';
 
    /**
     * Singleton instance
     * @var  HttpHeaders 
     */
    protected static $_instance = null;
    
    /**
     * The index \of the plugin in the controller dispatch loop plugin stack
     * @var integer
     */
    protected static $_controllerPluginStackIndex = 999;
     
    /**
     * The protocol instances \for this channel
     * @var array
     */
    protected $_protocols = null;
        
    /**
     * Initialize singleton instance.
     *
     * @param string $class OPTIONAL Subclass \of  HttpHeaders 
     * @return  HttpHeaders  Returns the singleton  HttpHeaders  instance
     * @throws  WildfireException 
     */
    public static function init($class = null)
    {
        if (self::$_instance!==null) {
            throw new  WildfireException ('Singleton instance \of  HttpHeaders  already exists!');
        }
        if ($class!==null) {
            if (!is_string($class)) {
                throw new  WildfireException ('Third argument is not a class string');
            }
             Loader ::loadClass($class);
            self::$_instance = new $class();
            if (!self::$_instance instanceof  HttpHeaders ) {
                self::$_instance = null;
                throw new  WildfireException ('Invalid class to third argument. Must be subclass \of  HttpHeaders .');
            }
        } else {
          self::$_instance = new self();
        }
        
        return self::$_instance;
    }


    /**
     * Get or create singleton instance
     * 
     * @param $skipCreate boolean True if an instance should not be created
     * @return  HttpHeaders 
     */
    public static function getInstance($skipCreate=false)
    {  
        if (self::$_instance===null && $skipCreate!==true) {
            return self::init();               
        }
        return self::$_instance;
    }
    
    /**
     * Destroys the singleton instance
     *
     * Primarily used \for testing.
     *
     * @return void
     */
    public static function destroyInstance()
    {
        self::$_instance = null;
    }
    
    /**
     * Get the instance \of a give protocol \for this channel
     * 
     * @param string $uri The URI \for the protocol
     * @return object Returns the protocol instance \for the diven URI
     */
    public function getProtocol($uri)
    {
        if (!isset($this->_protocols[$uri])) {
            $this->_protocols[$uri] = $this->_initProtocol($uri);
        }
 
        $this->_registerControllerPlugin();

        return $this->_protocols[$uri];
    }
    
    /**
     * Initialize a new protocol
     * 
     * @param string $uri The URI \for the protocol to be initialized
     * @return object Returns the new initialized protocol instance
     * @throws  WildfireException 
     */
    protected function _initProtocol($uri)
    {
        switch ($uri) {
            case  JsonStream ::PROTOCOL_URI;
                return new  JsonStream ();
        }
        throw new  WildfireException ('Tyring to initialize unknown protocol \for URI "'.$uri.'".');
    }
    
    
    /**
     * Flush all data from all protocols and send all data to response headers.
     *
     * @return boolean Returns TRUE if data was flushed
     */
    public function flush()
    {
        if (!$this->_protocols || !$this->isReady()) {
            return false;
        }

        foreach ( $this->_protocols as $protocol ) {

            $payload = $protocol->getPayload($this);

            if ($payload) {
                foreach( $payload as $message ) {

                    $this->getResponse()->setHeader(self::$_headerPrefix.$message[0],
                                                    $message[1], true);
                }
            }
        }
        return true;
    }
    
    /**
     * Set the index \of the plugin in the controller dispatch loop plugin stack
     * 
     * @param integer $index The index \of the plugin in the stack
     * @return integer The previous index.
     */
    public static function setControllerPluginStackIndex($index)
    {
        $previous = self::$_controllerPluginStackIndex;
        self::$_controllerPluginStackIndex = $index;
        return $previous;
    }

    /**
     * Register this object as a controller plugin.
     * 
     * @return void
     */
    protected function _registerControllerPlugin()
    {
        $controller =  Front ::getInstance();
        if (!$controller->hasPlugin(get_class($this))) {
            $controller->registerPlugin($this, self::$_controllerPluginStackIndex);
        }
    }

    
    /*
     *  WildfireChannelInterface  
     */

    /**
     * Determine if channel is ready.
     * 
     * @return boolean Returns TRUE if channel is ready.
     */
    public function isReady()
    {
        return ($this->getResponse()->canSendHeaders() &&
                preg_match_all('/\s?FirePHP\/([\.|\d]*)\s?/si',
                               $this->getRequest()->getHeader('\User-Agent'),$m));
    }


    /*
     * \Zend_Controller_Plugin_Abstract 
     */

    /**
     * Flush messages to headers as late as possible but before headers have been sent.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->flush();
    }
    
    /**
     * Get the request object
     * 
     * @return \Zend\Controller\Request\RequestAbstract
     * @throws  WildfireException 
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $controller =  Front ::getInstance();
            $this->setRequest($controller->getRequest());
        }
        if (!$this->_request) {
            throw new  WildfireException ('Request objects not initialized.');
        }
        return $this->_request;
    }

    /**
     * Get the response object
     * 
     * @return \Zend\Controller\Response\ResponseAbstract
     * @throws  WildfireException 
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $response =  Front ::getInstance()->getResponse();
            if ($response) {
                $this->setResponse($response);
            }
        }
        if (!$this->_response) {
            throw new  WildfireException ('Response objects not initialized.');
        }
        return $this->_response;
    }
}
