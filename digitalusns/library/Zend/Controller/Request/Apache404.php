<?php

namespace Zend\Controller\Request;


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
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** \Zend\Controller\Request\Exception */
require_once 'Zend/Controller/Request/Exception.php';

/**  Http  */
require_once 'Zend/Controller/Request/Http.php';

/** \Zend\Uri */
require_once 'Zend/Uri.php';

/**
 *  Apache404 
 *
 * HTTP request object \for use with Zend_Controller family. Extends basic HTTP
 * request object to allow \for two edge cases when using Apache:
 * - Using Apache's 404 handler instead \of mod_rewrite to direct requests
 * - Using the PT flag in rewrite rules
 *
 * In each case, the URL to check against is found in REDIRECT_URL, not
 * REQUEST_URI.
 *
 * @uses        Http 
 * @package    Zend_Controller
 * @subpackage Request
 */


use Zend\Controller\Request\Http as Http;




class  Apache404  extends  Http 
{
    public function setRequestUri($requestUri = null)
    {
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REDIRECT_URL'])) {  // Check if using mod_rewrite
                $requestUri = $_SERVER['REDIRECT_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                return $this;
            }
        } elseif (!is_string($requestUri)) {
            return $this;
        } else {
            // Set GET items, if available
            $_GET = array();
            if (false !== ($pos = strpos($requestUri, '?'))) {
                // Get key => value pairs and set $_GET
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $_GET = $vars;
            }
        }

        $this->_requestUri = $requestUri;
        return $this;
    }
}
