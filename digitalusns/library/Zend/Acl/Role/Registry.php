<?php

namespace Zend\Acl\Role;


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
 * @package    \Zend\Acl
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Registry.php 8861 2008-03-16 14:30:18Z thomas $
 */


/**
 * @see  AclRoleInterface 
 */
require_once 'Zend/Acl/Role/Interface.php';


/**
 * @category   Zend
 * @package    \Zend\Acl
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Acl\Role\Registry\Exception as AclRoleRegistryException;
use Zend\Acl\Role\RoleInterface as AclRoleInterface;




class  Registry 
{
    /**
     * Internal Role registry data storage
     *
     * @var array
     */
    protected $_roles = array();

    /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier \for,
     * a Role existing in the registry, or $parents may be passed as an array \of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param   AclRoleInterface               $role
     * @param   AclRoleInterface |string|array $parents
     * @throws  AclRoleRegistryException 
     * @return  Registry  Provides a fluent interface
     */
    public function add( AclRoleInterface  $role, $parents = null)
    {
        $roleId = $role->getRoleId();

        if ($this->has($roleId)) {
            /**
             * @see  AclRoleRegistryException 
             */
            require_once 'Zend/Acl/Role/Registry/Exception.php';
            throw new  AclRoleRegistryException ("Role id '$roleId' already exists in the registry");
        }

        $roleParents = array();

        if (null !== $parents) {
            if (!is_array($parents)) {
                $parents = array($parents);
            }
            /**
             * @see  AclRoleRegistryException 
             */
            require_once 'Zend/Acl/Role/Registry/Exception.php';
            foreach ($parents as $parent) {
                try {
                    if ($parent instanceof  AclRoleInterface ) {
                        $roleParentId = $parent->getRoleId();
                    } else {
                        $roleParentId = $parent;
                    }
                    $roleParent = $this->get($roleParentId);
                } catch ( AclRoleRegistryException  $e) {
                    throw new  AclRoleRegistryException ("Parent Role id '$roleParentId' does not exist");
                }
                $roleParents[$roleParentId] = $roleParent;
                $this->_roles[$roleParentId]['children'][$roleId] = $role;
            }
        }

        $this->_roles[$roleId] = array(
            'instance' => $role,
            'parents'  => $roleParents,
            'children' => array()
            );

        return $this;
    }

    /**
     * Returns the identified Role
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param   AclRoleInterface |string $role
     * @throws  AclRoleRegistryException 
     * @return  AclRoleInterface 
     */
    public function get($role)
    {
        if ($role instanceof  AclRoleInterface ) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = (string) $role;
        }

        if (!$this->has($role)) {
            /**
             * @see  AclRoleRegistryException 
             */
            require_once 'Zend/Acl/Role/Registry/Exception.php';
            throw new  AclRoleRegistryException ("Role '$roleId' not found");
        }

        return $this->_roles[$roleId]['instance'];
    }

    /**
     * Returns true if and only if the Role exists in the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param   AclRoleInterface |string $role
     * @return boolean
     */
    public function has($role)
    {
        if ($role instanceof  AclRoleInterface ) {
            $roleId = $role->getRoleId();
        } else {
            $roleId = (string) $role;
        }

        return isset($this->_roles[$roleId]);
    }

    /**
     * Returns an array \of an existing Role's parents
     *
     * The array keys are the identifiers \of the parent Roles, and the values are
     * the parent Role instances. The parent Roles are ordered in this array by
     * ascending priority. The highest priority parent Role, last in the array,
     * corresponds with the parent Role most recently added.
     *
     * If the Role does not have any parents, then an empty array is returned.
     *
     * @param   AclRoleInterface |string $role
     * @uses    Registry ::get()
     * @return array
     */
    public function getParents($role)
    {
        $roleId = $this->get($role)->getRoleId();

        return $this->_roles[$roleId]['parents'];
    }

    /**
     * Returns true if and only if $role inherits from $inherit
     *
     * Both parameters may be either a Role or a Role identifier. If
     * $onlyParents is true, then $role must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $role
     * inherits from $inherit through its ancestor Roles.
     *
     * @param   AclRoleInterface |string $role
     * @param   AclRoleInterface |string $inherit
     * @param  boolean                        $onlyParents
     * @throws  AclRoleRegistryException 
     * @return boolean
     */
    public function inherits($role, $inherit, $onlyParents = false)
    {
        /**
         * @see  AclRoleRegistryException 
         */
        require_once 'Zend/Acl/Role/Registry/Exception.php';
        try {
            $roleId     = $this->get($role)->getRoleId();
            $inheritId = $this->get($inherit)->getRoleId();
        } catch ( AclRoleRegistryException  $e) {
            throw $e;
        }

        $inherits = isset($this->_roles[$roleId]['parents'][$inheritId]);

        if ($inherits || $onlyParents) {
            return $inherits;
        }

        foreach ($this->_roles[$roleId]['parents'] as $parentId => $parent) {
            if ($this->inherits($parentId, $inheritId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the Role from the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param   AclRoleInterface |string $role
     * @throws  AclRoleRegistryException 
     * @return  Registry  Provides a fluent interface
     */
    public function remove($role)
    {
        /**
         * @see  AclRoleRegistryException 
         */
        require_once 'Zend/Acl/Role/Registry/Exception.php';
        try {
            $roleId = $this->get($role)->getRoleId();
        } catch ( AclRoleRegistryException  $e) {
            throw $e;
        }

        foreach ($this->_roles[$roleId]['children'] as $childId => $child) {
            unset($this->_roles[$childId]['parents'][$roleId]);
        }
        foreach ($this->_roles[$roleId]['parents'] as $parentId => $parent) {
            unset($this->_roles[$parentId]['children'][$roleId]);
        }

        unset($this->_roles[$roleId]);

        return $this;
    }

    /**
     * Removes all Roles from the registry
     *
     * @return  Registry  Provides a fluent interface
     */
    public function removeAll()
    {
        $this->_roles = array();

        return $this;
    }

}
