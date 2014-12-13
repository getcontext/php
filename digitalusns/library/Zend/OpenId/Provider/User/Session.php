<?php

namespace Zend\OpenId\Provider\User;



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
 * @package    \Zend\OpenId
 * @subpackage \Zend\OpenId\Provider
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Session.php 8064 2008-02-16 10:58:39Z thomas $
 */

/**
 * @see  OpenIdProviderUser 
 */
require_once "Zend/OpenId/Provider/User.php";

/**
 * @see  SessionNamespace 
 */
require_once "Zend/Session/Namespace.php";

/**
 * Class to get/store information about logged in user in Web Browser using
 * PHP session
 *
 * @category   Zend
 * @package    \Zend\OpenId
 * @subpackage \Zend\OpenId\Provider
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\OpenId\Provider\User as OpenIdProviderUser;
use Zend\Session\SessionNamespace as SessionNamespace;




class  Session  extends  OpenIdProviderUser 
{
    /**
     * \Reference to an implementation \of  SessionNamespace  object
     *
     * @var  SessionNamespace  $_session
     */
    private $_session = null;

    /**
     * Creates  Session  object with given session
     * namespace or creates new session namespace named "openid"
     *
     * @param  SessionNamespace  $session
     */
    public function __construct( SessionNamespace  $session = null)
    {
        if ($session === null) {
            $this->_session = new  SessionNamespace ("openid");
        } else {
            $this->_session = $session;
        }
    }

    /**
     * Stores information about logged in user in session data
     *
     * @param string $id user identity URL
     * @return bool
     */
    public function setLoggedInUser($id)
    {
        $this->_session->logged_in = $id;
        return true;
    }

    /**
     * Returns identity URL \of logged in user or false
     *
     * @return mixed
     */
    public function getLoggedInUser()
    {
        if (isset($this->_session->logged_in)) {
            return $this->_session->logged_in;
        }
        return false;
    }

    /**
     * Performs logout. Clears information about logged in user.
     *
     * @return bool
     */
    public function delLoggedInUser()
    {
        unset($this->_session->logged_in);
        return true;
    }

}
