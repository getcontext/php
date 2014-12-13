<?php

namespace Zend\Auth\Adapter;


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
 * @subpackage \Zend\Auth_Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Digest.php 9668 2008-06-11 08:15:02Z doctorrock83 $
 */


/**
 * @see  AuthAdapterInterface 
 */
require_once 'Zend/Auth/Adapter/Interface.php';


/**
 * @category   Zend
 * @package    \Zend\Auth
 * @subpackage \Zend\Auth_Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Auth\Adapter\AdapterInterface as AuthAdapterInterface;
use Zend\Auth\Adapter\Exception as AuthAdapterException;
use Zend\Auth\Result as Result;




class  Digest  implements  AuthAdapterInterface 
{
    /**
     * Filename against which authentication queries are performed
     *
     * @var string
     */
    protected $_filename;

    /**
     * Digest authentication realm
     *
     * @var string
     */
    protected $_realm;

    /**
     * Digest authentication user
     *
     * @var string
     */
    protected $_username;

    /**
     * Password \for the user \of the realm
     *
     * @var string
     */
    protected $_password;

    /**
     * Sets adapter options
     *
     * @param  mixed $filename
     * @param  mixed $realm
     * @param  mixed $username
     * @param  mixed $password
     * @return void
     */
    public function __construct($filename = null, $realm = null, $username = null, $password = null)
    {
        $options = array('filename', 'realm', 'username', 'password');
        foreach ($options as $option) {
            if (null !== $$option) {
                $methodName = 'set' . ucfirst($option);
                $this->$methodName($$option);
            }
        }
    }

    /**
     * Returns the filename option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * Sets the filename option value
     *
     * @param  mixed $filename
     * @return  Digest  Provides a fluent interface
     */
    public function setFilename($filename)
    {
        $this->_filename = (string) $filename;
        return $this;
    }

    /**
     * Returns the realm option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getRealm()
    {
        return $this->_realm;
    }

    /**
     * Sets the realm option value
     *
     * @param  mixed $realm
     * @return  Digest  Provides a fluent interface
     */
    public function setRealm($realm)
    {
        $this->_realm = (string) $realm;
        return $this;
    }

    /**
     * Returns the username option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the username option value
     *
     * @param  mixed $username
     * @return  Digest  Provides a fluent interface
     */
    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }

    /**
     * Returns the password option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Sets the password option value
     *
     * @param  mixed $password
     * @return  Digest  Provides a fluent interface
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }

    /**
     * Defined by  AuthAdapterInterface 
     *
     * @throws  AuthAdapterException 
     * @return  Result 
     */
    public function authenticate()
    {
        $optionsRequired = array('filename', 'realm', 'username', 'password');
        foreach ($optionsRequired as $optionRequired) {
            if (null === $this->{"_$optionRequired"}) {
                /**
                 * @see  AuthAdapterException 
                 */
                require_once 'Zend/Auth/Adapter/Exception.php';
                throw new  AuthAdapterException ("Option '$optionRequired' must be set before authentication");
            }
        }

        if (false === ($fileHandle = @fopen($this->_filename, 'r'))) {
            /**
             * @see  AuthAdapterException 
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new  AuthAdapterException ("Cannot open '$this->_filename' \for reading");
        }

        $id       = "$this->_username:$this->_realm";
        $idLength = strlen($id);

        $result = array(
            'code'  =>  Result ::FAILURE,
            'identity' => array(
                'realm'    => $this->_realm,
                'username' => $this->_username,
                ),
            'messages' => array()
            );

        while ($line = trim(fgets($fileHandle))) {
            if (substr($line, 0, $idLength) === $id) {
                if (substr($line, -32) === md5("$this->_username:$this->_realm:$this->_password")) {
                    $result['code'] =  Result ::SUCCESS;
                } else {
                    $result['code'] =  Result ::FAILURE_CREDENTIAL_INVALID;
                    $result['messages'][] = 'Password incorrect';
                }
                return new  Result ($result['code'], $result['identity'], $result['messages']);
            }
        }

        $result['code'] =  Result ::FAILURE_IDENTITY_NOT_FOUND;
        $result['messages'][] = "Username '$this->_username' and realm '$this->_realm' combination not found";
        return new  Result ($result['code'], $result['identity'], $result['messages']);
    }
}
