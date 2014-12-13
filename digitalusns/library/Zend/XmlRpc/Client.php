<?php

namespace Zend\XmlRpc;


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
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * For handling the HTTP connection to the XML-RPC service
 */
require_once 'Zend/Http/Client.php';

/**
 * \Exception thrown when an HTTP error occurs
 */
require_once 'Zend/XmlRpc/Client/HttpException.php';

/**
 * \Exception thrown when an XML-RPC fault is returned
 */
require_once 'Zend/XmlRpc/Client/FaultException.php';

/**
 * Enables object chaining \for calling namespaced XML-RPC methods.
 */
require_once 'Zend/XmlRpc/Client/ServerProxy.php';

/**
 * Introspects remote servers using the XML-RPC de facto system.* methods
 */
require_once 'Zend/XmlRpc/Client/ServerIntrospection.php';

/**
 * Represent a native XML-RPC value, used both in sending parameters
 * to methods and as the parameters retrieve from method calls
 */
require_once 'Zend/XmlRpc/Value.php';

/**
 * XML-RPC Request
 */
require_once 'Zend/XmlRpc/Request.php';

/**
 * XML-RPC Response
 */
require_once 'Zend/XmlRpc/Response.php';

/**
 * XML-RPC Fault
 */
require_once 'Zend/XmlRpc/Fault.php';


/**
 * An XML-RPC client implementation
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\XmlRpc\Client\ServerIntrospection as ServerIntrospection;
use Zend\XmlRpc\Client\FaultException as FaultException;
use Zend\XmlRpc\Client\HttpException as HttpException;
use Zend\XmlRpc\Client\ServerProxy as ServerProxy;
use Zend\XmlRpc\Exception as XmlRpcException;
use Zend\XmlRpc\Response as Response;
use Zend\XmlRpc\Request as Request;
use Zend\Http\Client as HttpClient;




class  Client 
{
    /** @var string */
    private $_serverAddress;

    /** @var  HttpClient  */
    private $_httpClient = null;

    /** @var  HttpClient _Introspector */
    private $_introspector = null;

    /** @var  Request  */
    private $_lastRequest = null;

    /** @var  Response  */
    private $_lastResponse = null;

    /** @var array \of  ServerProxy  */
    private $_proxyCache = array();

    /** @var bool */
    private $_skipSystemLookup = false;

    /**
     * Create a new XML-RPC client to a remote server
     *
     * @param  string $server      Full address \of the XML-RPC service
     *                             (e.g. http://time.xmlrpc.com/RPC2)
     * @param   HttpClient  $httpClient HTTP Client to use \for requests
     * @return void
     */
    public function __construct($server,  HttpClient  $httpClient = null)
    {
        if ($httpClient === null) {
            $this->_httpClient = new  HttpClient ();
        } else {
            $this->_httpClient = $httpClient;
        }

        $this->_introspector  = new  ServerIntrospection ($this);
        $this->_serverAddress = $server;
    }


    /**
     * Sets the HTTP client object to use \for connecting the XML-RPC server.
     *
     * @param   HttpClient  $httpClient
     * @return  HttpClient 
     */
    public function setHttpClient( HttpClient  $httpClient)
    {
        return $this->_httpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object.
     *
     * @return  HttpClient 
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }


    /**
     * Sets the object used to introspect remote servers
     *
     * @param   ServerIntrospection 
     * @return  ServerIntrospection 
     */
    public function setIntrospector( ServerIntrospection  $introspector)
    {
        return $this->_introspector = $introspector;
    }


    /**
     * Gets the introspection object.
     *
     * @return  ServerIntrospection 
     */
    public function getIntrospector()
    {
        return $this->_introspector;
    }


   /**
     * The request \of the last method call
     *
     * @return  Request 
     */
    public function getLastRequest()
    {
        return $this->_lastRequest;
    }


    /**
     * The response received from the last method call
     *
     * @return  Response 
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }


    /**
     * Returns a proxy object \for more convenient method calls
     *
     * @param $namespace  Namespace to proxy or empty string \for none
     * @return  ServerProxy 
     */
    public function getProxy($namespace = '')
    {
        if (empty($this->_proxyCache[$namespace])) {
            $proxy = new  ServerProxy ($this, $namespace);
            $this->_proxyCache[$namespace] = $proxy;
        }
        return $this->_proxyCache[$namespace];
    }

    /**
     * Set skip system lookup flag
     * 
     * @param  bool $flag 
     * @return  Client 
     */
    public function setSkipSystemLookup($flag = true)
    {
        $this->_skipSystemLookup = (bool) $flag;
        return $this;
    }

    /**
     * Skip system lookup when determining if parameter should be array or struct?
     * 
     * @return bool
     */
    public function skipSystemLookup()
    {
        return $this->_skipSystemLookup;
    }

    /**
     * Perform an XML-RPC request and return a response.
     *
     * @param  Request  $request
     * @param null| Response  $response
     * @return void
     */
    public function doRequest($request, $response = null)
    {
        $this->_lastRequest = $request;

        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $http = $this->getHttpClient();
        $http->setUri($this->_serverAddress);

        $http->setHeaders(array(
            'Content-Type: text/xml; charset=utf-8',
            '\User-Agent: \Zend\XmlRpc\Client',
            'Accept: text/xml',
        ));

        $xml = $this->_lastRequest->__toString();
        $http->setRawData($xml);
        $httpResponse = $http->request( HttpClient ::POST);

        if (! $httpResponse->isSuccessful()) {
            throw new  HttpException (
                                    $httpResponse->getMessage(),
                                    $httpResponse->getStatus());
        }

        if ($response === null) {
            $response = new  Response ();
        }
        $this->_lastResponse = $response;
        $this->_lastResponse->loadXml($httpResponse->getBody());
    }

    /**
     * Send an XML-RPC request to the service (\for a specific method)
     *
     * @param string $method Name \of the method we want to call
     * @param array $params Array \of parameters \for the method
     * @throws  HttpClient _FaultException
     */
    public function call($method, $params=array())
    {
        if (!$this->skipSystemLookup() && ('system.' != substr($method, 0, 7))) {
            // Ensure empty array/struct params are cast correctly
            // If system.* methods are not available, bypass. (ZF-2978)
            $success = true;
            try {
                $signatures = $this->getIntrospector()->getMethodSignature($method);
            } catch ( XmlRpcException  $e) {
                $success = false;
            }
            if ($success) {
                foreach ($params as $key => $param) {
                    if (is_array($param) && empty($param)) {
                        $type = 'array';
                        foreach ($signatures as $signature) {
                            if (!is_array($signature)) {
                                continue;
                            }
                            if (array_key_exists($key + 1, $signature)) {
                                $type = $signature[$key + 1];
                                $type = (in_array($type, array('array', 'struct'))) ? $type : 'array';
                                break;
                            }
                        }
                        $params[$key] = array(
                            'type'  => $type, 
                            'value' => $param
                        );
                    } 
                }
            }
        }

        $request = new  Request ($method, $params);

        $this->doRequest($request);

        if ($this->_lastResponse->isFault()) {
            $fault = $this->_lastResponse->getFault();
            throw new  FaultException ($fault->getMessage(),
                                                        $fault->getCode());
        }

        return $this->_lastResponse->getReturnValue();
    }
}
