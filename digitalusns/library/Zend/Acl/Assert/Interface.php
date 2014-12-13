<?php

namespace Zend\Acl\Assert;


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
 * @package     Acl 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 8861 2008-03-16 14:30:18Z thomas $
 */


/**
 * @see  Acl 
 */
require_once 'Zend/Acl.php';


/**
 * @see  AclRoleInterface 
 */
require_once 'Zend/Acl/Role/Interface.php';


/**
 * @see  AclResourceInterface 
 */
require_once 'Zend/Acl/Resource/Interface.php';


/**
 * @category   Zend
 * @package     Acl 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Acl\Resource\ResourceInterface as AclResourceInterface;
use Zend\Acl\Role\RoleInterface as AclRoleInterface;
use Zend\Acl as Acl;




interface  AssertInterface 
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param   Acl                     $acl
     * @param   AclRoleInterface      $role
     * @param   AclResourceInterface  $resource
     * @param  string                      $privilege
     * @return boolean
     */
    public function assert( Acl  $acl,  AclRoleInterface  $role = null,  AclResourceInterface  $resource = null,
                           $privilege = null);
}
