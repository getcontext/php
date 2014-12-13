<?php

namespace Zend\Gdata\App;



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
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 *  GdataAppException 
 */
require_once 'Zend/Gdata/App/Exception.php';

/**
 *  HttpClientException 
 */
require_once 'Zend/Http/Client/Exception.php';

/**
 * Gdata exceptions
 *
 * Class to represent exceptions that occur during Gdata operations.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Http\Client\Exception as HttpClientException;
use Zend\Gdata\App\Exception as GdataAppException;
use Zend\Http\Response as Response;




class  HttpException  extends  GdataAppException 
{

    protected $_httpClientException = null;
    protected $_response = null;

    /**
     * Create a new  HttpException 
     *
     * @param  string $message Optionally set a message
     * @param  HttpClientException  Optionally pass in a  HttpClientException 
     * @param  Response  Optionally pass in a  Response 
     */
    public function __construct($message = null, $e = null, $response = null)
    {
        $this->_httpClientException = $e;
        $this->_response = $response;
        parent::__construct($message);
    }

    /**
     * Get the  HttpClientException .
     *
     * @return  HttpClientException 
     */
    public function getHttpClientException()
    {
        return $this->_httpClientException;
    }

    /**
     * Set the  HttpClientException .
     *
     * @param  HttpClientException  $value
     */
    public function setHttpClientException($value)
    {
        $this->_httpClientException = $value;
        return $this;
    }

    /**
     * Set the  Response .
     *
     * @param  Response  $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get the  Response .
     *
     * @return  Response 
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get the body \of the  Response 
     *
     * @return string
     */
    public function getRawResponseBody()
    {
        if ($this->getResponse()) {
            $response = $this->getResponse();
            return $response->getRawBody();
        }
        return null;
    }

}
