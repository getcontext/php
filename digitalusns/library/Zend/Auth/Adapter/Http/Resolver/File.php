<?php

namespace Zend\Auth\Adapter\Http\Resolver;


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
 * @package    \Zend\Auth
 * @subpackage \Zend\Auth\Adapter\Http
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: File.php 8862 2008-03-16 15:36:00Z thomas $
 */


/**
 * @see  AuthAdapterHttpResolverInterface 
 */
require_once 'Zend/Auth/Adapter/Http/Resolver/Interface.php';


/**
 * HTTP Authentication File Resolver
 *
 * @category   Zend
 * @package    \Zend\Auth
 * @subpackage \Zend\Auth\Adapter\Http
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Auth\Adapter\Http\Resolver\ResolverInterface as AuthAdapterHttpResolverInterface;
use Zend\Auth\Adapter\Http\Resolver\Exception as AuthAdapterHttpResolverException;




class  File  implements  AuthAdapterHttpResolverInterface 
{
    /**
     * Path to credentials file
     *
     * @var string
     */
    protected $_file;

    /**
     * Constructor
     *
     * @param  string $path Complete filename where the credentials are stored
     * @return void
     */
    public function __construct($path = '')
    {
        if (!empty($path)) {
            $this->setFile($path);
        }
    }

    /**
     * Set the path to the credentials file
     *
     * @param  string $path
     * @throws  AuthAdapterHttpResolverException 
     * @return  File  Provides a fluent interface
     */
    public function setFile($path)
    {
        if (empty($path) || !is_readable($path)) {
            /**
             * @see  AuthAdapterHttpResolverException 
             */
            require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';
            throw new  AuthAdapterHttpResolverException ('Path not readable: ' . $path);
        }
        $this->_file = $path;

        return $this;
    }

    /**
     * Returns the path to the credentials file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Resolve credentials
     *
     * Only the first matching username/realm combination in the file is
     * returned. If the file contains credentials \for Digest authentication,
     * the returned string is the password hash, or h(a1) from RFC 2617. The
     * returned string is the plain-text password \for Basic authentication.
     *
     * The expected format \of the file is:
     *   username:realm:sharedSecret
     *
     * That is, each line consists \of the user's username, the applicable
     * authentication realm, and the password or hash, each delimited by
     * colons.
     *
     * @param  string $username Username
     * @param  string $realm    Authentication Realm
     * @throws  AuthAdapterHttpResolverException 
     * @return string|false \User's shared secret, if the user is found in the
     *         realm, false otherwise.
     */
    public function resolve($username, $realm)
    {
        if (empty($username)) {
            /**
             * @see  AuthAdapterHttpResolverException 
             */
            require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';
            throw new  AuthAdapterHttpResolverException ('Username is required');
        } else if (!ctype_print($username) || strpos($username, ':') !== false) {
            /**
             * @see  AuthAdapterHttpResolverException 
             */
            require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';
            throw new  AuthAdapterHttpResolverException ('Username must consist only \of printable characters, '
                                                              . 'excluding the colon');
        }
        if (empty($realm)) {
            /**
             * @see  AuthAdapterHttpResolverException 
             */
            require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';
            throw new  AuthAdapterHttpResolverException ('Realm is required');
        } else if (!ctype_print($realm) || strpos($realm, ':') !== false) {
            /**
             * @see  AuthAdapterHttpResolverException 
             */
            require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';
            throw new  AuthAdapterHttpResolverException ('Realm must consist only \of printable characters, '
                                                              . 'excluding the colon.');
        }

        // Open file, read through looking \for matching credentials
        $fp = @fopen($this->_file, 'r');
        if (!$fp) {
            /**
             * @see  AuthAdapterHttpResolverException 
             */
            require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';
            throw new  AuthAdapterHttpResolverException ('Unable to open password file: ' . $this->_file);
        }

        // No real validation is done on the contents \of the password file. The
        // assumption is that we trust the administrators to keep it secure.
        while (($line = fgetcsv($fp, 512, ':')) !== false) {
            if ($line[0] == $username && $line[1] == $realm) {
                $password = $line[2];
                fclose($fp);
                return $password;
            }
        }

        fclose($fp);
        return false;
    }
}
