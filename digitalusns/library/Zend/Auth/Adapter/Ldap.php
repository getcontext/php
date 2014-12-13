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
 * @version    $Id: Ldap.php 10171 2008-07-18 04:57:08Z miallen $
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
use Zend\Ldap\Exception as LdapException;
use Zend\Auth\Result as Result;
use Zend\Ldap as ZendLdap;




class  Ldap  implements  AuthAdapterInterface 
{

    /**
     * The  ZendLdap  context.
     *
     * @var  ZendLdap 
     */
    protected $_ldap = null;

    /**
     * The array \of arrays \of  ZendLdap  options passed to the constructor.
     *
     * @var array
     */
    protected $_options = null;

    /**
     * The username \of the account being authenticated.
     *
     * @var string
     */
    protected $_username = null;

    /**
     * The password \of the account being authenticated.
     *
     * @var string
     */
    protected $_password = null;

    /**
     * Constructor
     *
     * @param  array  $options  An array \of arrays \of  ZendLdap  options
     * @param  string $username The username \of the account being authenticated
     * @param  string $password The password \of the account being authenticated
     * @return void
     */
    public function __construct(array $options = array(), $username = null, $password = null)
    {
        $this->setOptions($options);
        if ($username !== null) {
            $this->setUsername($username);
        }
        if ($password !== null) {
            $this->setPassword($password);
        }
    }

    /**
     * Returns the array \of arrays \of  ZendLdap  options \of this adapter.
     *
     * @return array|null
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets the array \of arrays \of  ZendLdap  options to be used by
     * this adapter.
     *
     * @param  array $options The array \of arrays \of  ZendLdap  options
     * @return  Ldap  Provides a fluent interface
     */
    public function setOptions($options)
    {
        $this->_options = is_array($options) ? $options : array();
        return $this;
    }

    /**
     * Returns the username \of the account being authenticated, or
     * NULL if none is set.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the username \for binding
     *
     * @param  string $username The username \for binding
     * @return  Ldap  Provides a fluent interface
     */
    public function setUsername($username)
    {
        $this->_username = (string) $username;
        return $this;
    }

    /**
     * Returns the password \of the account being authenticated, or
     * NULL if none is set.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Sets the passwort \for the account
     *
     * @param  string $password The password \of the account being authenticated
     * @return  Ldap  Provides a fluent interface
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }

    /**
     * Returns the LDAP Object
     *
     * @return  ZendLdap  The  ZendLdap  object used to authenticate the credentials
     */
    public function getLdap()
    {
        if ($this->_ldap === null) {
            /**
             * @see  ZendLdap 
             */
            require_once 'Zend/Ldap.php';
            $this->_ldap = new  ZendLdap ();
        }
        return $this->_ldap;
    }

    /**
     * Authenticate the user
     *
     * @throws  AuthAdapterException 
     * @return  Result 
     */
    public function authenticate()
    {
        /**
         * @see  LdapException 
         */
        require_once 'Zend/Ldap/Exception.php';

        $messages = array();
        $messages[0] = ''; // reserved
        $messages[1] = ''; // reserved

        $username = $this->_username;
        $password = $this->_password;

        if (!$username) {
            $code =  Result ::FAILURE_IDENTITY_NOT_FOUND;
            $messages[0] = 'A username is required';
            return new  Result ($code, '', $messages);
        }
        if (!$password) {
            /* A password is required because some servers will
             * treat an empty password as an anonymous bind.
             */
            $code =  Result ::FAILURE_CREDENTIAL_INVALID;
            $messages[0] = 'A password is required';
            return new  Result ($code, '', $messages);
        }

        $ldap = $this->getLdap();

        $code =  Result ::FAILURE;
        $messages[0] = "Authority not found: $username";

        /* Iterate through each server and try to authenticate the supplied
         * credentials against it.
         */
        foreach ($this->_options as $name => $options) {

            if (!is_array($options)) {
                /**
                 * @see  AuthAdapterException 
                 */
                require_once 'Zend/Auth/Adapter/Exception.php';
                throw new  AuthAdapterException ('Adapter options array not in array');
            }
            $ldap->setOptions($options);

            try {

                $canonicalName = $ldap->getCanonicalAccountName($username);

                if ($messages[1])
                    $messages[] = $messages[1];
                $messages[1] = '';
                $messages[] = $this->_optionsToString($options);

                $ldap->bind($canonicalName, $password);

                $messages[0] = '';
                $messages[1] = '';
                $messages[] = "$canonicalName authentication successful";

                return new  Result (\Zend\Auth\Result::SUCCESS, $canonicalName, $messages);
            } catch ( LdapException  $zle) {

                /* LDAP based authentication is notoriously difficult to diagnose. Therefore
                 * we bend over backwards to capture and record every possible bit \of
                 * information when something goes wrong.
                 */

                $err = $zle->getCode();

                if ($err ==  LdapException ::LDAP_X_DOMAIN_MISMATCH) {
                    /* This error indicates that the domain supplied in the
                     * username did not match the domains in the server options
                     * and therefore we should just skip to the next set \of
                     * server options.
                     */
                    continue;
                } else if ($err ==  LdapException ::LDAP_NO_SUCH_OBJECT) {
                    $code =  Result ::FAILURE_IDENTITY_NOT_FOUND;
                    $messages[0] = "Account not found: $username";
                } else if ($err ==  LdapException ::LDAP_INVALID_CREDENTIALS) {
                    $code =  Result ::FAILURE_CREDENTIAL_INVALID;
                    $messages[0] = 'Invalid credentials';
                } else {
                    $line = $zle->getLine();
                    $messages[] = $zle->getFile() . "($line): " . $zle->getMessage();
                    $messages[] = str_replace($password, '*****', $zle->getTraceAsString());
                    $messages[0] = 'An unexpected failure occurred';
                }
                $messages[1] = $zle->getMessage();
            }
        }

        $msg = isset($messages[1]) ? $messages[1] : $messages[0];
        $messages[] = "$username authentication failed: $msg";

        return new  Result ($code, $username, $messages);
    }

    /**
     * Converts options to string
     *
     * @param  array $options
     * @return string
     */
    private function _optionsToString(array $options)
    {
        $str = '';
        foreach ($options as $key => $val) {
            if ($key === 'password')
                $val = '*****';
            if ($str)
                $str .= ',';
            $str .= $key . '=' . $val;
        }
        return $str;
    }
}
