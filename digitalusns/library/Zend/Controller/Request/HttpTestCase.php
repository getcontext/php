<?php

namespace Zend\Controller\Request;


/**  Http  */
require_once 'Zend/Controller/Request/Http.php';


use Zend\Controller\Request\Http as Http;
use Zend\Controller\Exception as ControllerException;





class  HttpTestCase  extends  Http 
{
    /**
     * Request headers
     * @var array
     */
    protected $_headers = array();

    /**
     * Request method
     * @var string
     */
    protected $_method;

    /**
     * Raw POST body
     * @var string|null
     */
    protected $_rawBody;

    /**
     * Valid request method types
     * @var array
     */
    protected $_validMethodTypes = array(
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'POST',
        'PUT',
    );

    /**
     * Set GET values
     * 
     * @param  string|array $spec 
     * @param  null|mixed $value 
     * @return  HttpTestCase 
     */
    public function setQuery($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            require_once 'Zend/Controller/Exception.php';
            throw new  ControllerException ('Invalid value passed to setQuery(); must be either array \of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setQuery($key, $value);
            }
            return $this;
        }
        $_GET[(string) $spec] = $value;
        return $this;
    }

    /**
     * Clear GET values
     * 
     * @return  HttpTestCase 
     */
    public function clearQuery()
    {
        $_GET = array();
        return $this;
    }

    /**
     * Set POST values
     * 
     * @param  string|array $spec 
     * @param  null|mixed $value 
     * @return  HttpTestCase 
     */
    public function setPost($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            require_once 'Zend/Controller/Exception.php';
            throw new  ControllerException ('Invalid value passed to setPost(); must be either array \of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setPost($key, $value);
            }
            return $this;
        }
        $_POST[(string) $spec] = $value;
        return $this;
    }

    /**
     * Clear POST values
     * 
     * @return  HttpTestCase 
     */
    public function clearPost()
    {
        $_POST = array();
        return $this;
    }

    /**
     * Set raw POST body
     * 
     * @param  string $content 
     * @return  HttpTestCase 
     */
    public function setRawBody($content)
    {
        $this->_rawBody = (string) $content;
        return $this;
    }

    /**
     * Get RAW POST body
     * 
     * @return string|null
     */
    public function getRawBody()
    {
        return $this->_rawBody;
    }

    /**
     * Clear raw POST body
     * 
     * @return  HttpTestCase 
     */
    public function clearRawBody()
    {
        $this->_rawBody = null;
        return $this;
    }

    /**
     * Set a cookie
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return  HttpTestCase 
     */
    public function setCookie($key, $value)
    {
        $_COOKIE[(string) $key] = $value;
        return $this;
    }

    /**
     * Set multiple cookies at once
     * 
     * @param array $cookies 
     * @return void
     */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $key => $value) {
            $_COOKIE[$key] = $value;
        }
        return $this;
    }

    /**
     * Clear all cookies
     * 
     * @return  HttpTestCase 
     */
    public function clearCookies()
    {
        $_COOKIE = array();
        return $this;
    }

    /**
     * Set request method
     * 
     * @param  string $type 
     * @return  HttpTestCase 
     */
    public function setMethod($type)
    {
        $type = strtoupper(trim((string) $type));
        if (!in_array($type, $this->_validMethodTypes)) {
            require_once 'Zend/Controller/Exception.php';
            throw new  ControllerException ('Invalid request method specified');
        }
        $this->_method = $type;
        return $this;
    }

    /**
     * Get request method
     * 
     * @return string|null
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Set a request header
     * 
     * @param  string $key 
     * @param  string $value 
     * @return  HttpTestCase 
     */
    public function setHeader($key, $value)
    {
        $key = $this->_normalizeHeaderName($key);
        $this->_headers[$key] = (string) $value;
        return $this;
    }

    /**
     * Set request headers
     * 
     * @param  array $headers 
     * @return  HttpTestCase 
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    /**
     * Get request header
     * 
     * @param  string $header 
     * @param  mixed $default 
     * @return string|null
     */
    public function getHeader($header, $default = null)
    {
        $header = $this->_normalizeHeaderName($header);
        if (array_key_exists($header, $this->_headers)) {
            return $this->_headers[$header];
        }
        return $default;
    }

    /**
     * Get all request headers
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Clear request headers
     * 
     * @return  HttpTestCase 
     */
    public function clearHeaders()
    {
        $this->_headers = array();
        return $this;
    }

    /**
     * Get REQUEST_URI
     * 
     * @return null|string
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Normalize a header name \for setting and retrieval
     * 
     * @param  string $name 
     * @return string
     */
    protected function _normalizeHeaderName($name)
    {
        $name = strtoupper((string) $name);
        $name = str_replace('-', '_', $name);
        return $name;
    }
}
