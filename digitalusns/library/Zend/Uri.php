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
 * @category  Zend
 * @package    Uri 
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Uri.php 9656 2008-06-10 16:21:13Z dasprid $
 */

/**
 * @see  Loader 
 */
require_once 'Zend/Loader.php';

/**
 * Abstract class \for all  Uri  handlers
 *
 * @category  Zend
 * @package    Uri 
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Uri\Exception as UriException;
use Zend\Loader as Loader;



abstract class  Uri 
{
    /**
     * Scheme \of this URI (http, ftp, etc.)
     *
     * @var string
     */
    protected $_scheme = '';

    /**
     * Return a string representation \of this URI.
     *
     * @see    getUri()
     * @return string
     */
    public function __toString()
    {
        return $this->getUri();
    }

    /**
     * Convenience function, checks that a $uri string is well-formed
     * by validating it but not returning an object.  Returns TRUE if
     * $uri is a well-formed URI, or FALSE otherwise.
     *
     * @param  string $uri The URI to check
     * @return boolean
     */
    public static function check($uri)
    {
        try {
            $uri = self::factory($uri);
        } catch (\Exception $e) {
            return false;
        }

        return $uri->valid();
    }

    /**
     * Create a new  Uri  object \for a URI.  If building a new URI, then $uri should contain
     * only the scheme (http, ftp, etc).  Otherwise, supply $uri with the complete URI.
     *
     * @param  string $uri The URI form which a  Uri  instance is created
     * @throws  UriException  When an empty string was supplied \for the scheme
     * @throws  UriException  When an illegal scheme is supplied
     * @throws  UriException  When the scheme is not supported
     * @return  Uri 
     * @link   http://www.faqs.org/rfcs/rfc2396.html
     */
    public static function factory($uri = 'http')
    {
        // Separate the scheme from the scheme-specific parts
        $uri            = explode(':', $uri, 2);
        $scheme         = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

        if (strlen($scheme) === 0) {
            require_once 'Zend/Uri/Exception.php';
            throw new  UriException ('An empty string was supplied \for the scheme');
        }

        // Security check: $scheme is used to load a class file, so only alphanumerics are allowed.
        if (ctype_alnum($scheme) === false) {
            require_once 'Zend/Uri/Exception.php';
            throw new  UriException ('Illegal scheme supplied, only alphanumeric characters are permitted');
        }

        /**
         * Create a new  Uri  object \for the $uri. If a subclass \of  Uri  exists \for the
         * scheme, return an instance \of that class. Otherwise, a  UriException  is thrown.
         */
        switch ($scheme) {
            case 'http':
                // Break intentionally omitted
            case 'https':
                $className = '\Zend\Uri\Http';
                break;

            case 'mailto':
                // TODO
            default:
                require_once 'Zend/Uri/Exception.php';
                throw new  UriException ("Scheme \"$scheme\" is not supported");
                break;
        }

         Loader ::loadClass($className);
        $schemeHandler = new $className($scheme, $schemeSpecific);

        return $schemeHandler;
    }

    /**
     * Get the URI's scheme
     *
     * @return string|false Scheme or false if no scheme is set.
     */
    public function getScheme()
    {
        if (empty($this->_scheme) === false) {
            return $this->_scheme;
        } else {
            return false;
        }
    }

    /**
     *  Uri  and its subclasses cannot be instantiated directly.
     * Use  Uri ::factory() to return a new  Uri  object.
     *
     * @param string $scheme         The scheme \of the URI
     * @param string $schemeSpecific The scheme-specific part \of the URI
     */
    abstract protected function __construct($scheme, $schemeSpecific = '');

    /**
     * Return a string representation \of this URI.
     *
     * @return string
     */
    abstract public function getUri();

    /**
     * Returns TRUE if this URI is valid, or FALSE otherwise.
     *
     * @return boolean
     */
    abstract public function valid();
}
