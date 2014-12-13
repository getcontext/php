<?php

namespace Zend\Controller\Plugin;


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
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** \Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**  Registry  */
require_once 'Zend/Registry.php';

/**
 * Manage a stack \of actions
 *
 * @uses       \Zend_Controller_Plugin_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: ActionStack.php 8064 2008-02-16 10:58:39Z thomas $
 */


use Zend\Controller\Request\RequestAbstract as ControllerRequestAbstract;
use Zend\Controller\Exception as ControllerException;
use Zend\Registry as Registry;




class  ActionStack  extends \Zend_Controller_Plugin_Abstract
{
    /** @var  Registry  */
    protected $_registry;

    /**
     * Registry key under which actions are stored
     * @var string
     */
    protected $_registryKey = '\Zend\Controller\Plugin\ActionStack';

    /**
     * Valid keys \for stack items
     * @var array
     */
    protected $_validKeys = array(
        'module', 
        'controller',
        'action',
        'params'
    );

    /**
     * Constructor
     *
     * @param   Registry  $registry
     * @param  string $key
     * @return void
     */
    public function __construct( Registry  $registry = null, $key = null)
    {
        if (null === $registry) {
            $registry =  Registry ::getInstance();
        }
        $this->setRegistry($registry);

        if (null !== $key) {
            $this->setRegistryKey($key);
        } else {
            $key = $this->getRegistryKey();
        }

        $registry[$key] = array();
    }

    /**
     * Set registry object
     * 
     * @param   Registry  $registry 
     * @return  ActionStack 
     */
    public function setRegistry( Registry  $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Retrieve registry object
     * 
     * @return  Registry 
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Retrieve registry key
     *
     * @return string
     */
    public function getRegistryKey()
    {
        return $this->_registryKey;
    }

    /**
     * Set registry key
     *
     * @param  string $key
     * @return  ActionStack 
     */
    public function setRegistryKey($key)
    {
        $this->_registryKey = (string) $key;
        return $this;
    }

    /**
     * Retrieve action stack
     * 
     * @return array
     */
    public function getStack()
    {
        $registry = $this->getRegistry();
        $stack    = $registry[$this->getRegistryKey()];
        return $stack;
    }

    /**
     * Save stack to registry
     * 
     * @param  array $stack 
     * @return  ActionStack 
     */
    protected function _saveStack(array $stack)
    {
        $registry = $this->getRegistry();
        $registry[$this->getRegistryKey()] = $stack;
        return $this;
    }

    /**
     * Push an item onto the stack
     * 
     * @param   ControllerRequestAbstract  $next 
     * @return  ActionStack 
     */
    public function pushStack( ControllerRequestAbstract  $next)
    {
        $stack = $this->getStack();
        array_push($stack, $next);
        return $this->_saveStack($stack);
    }

    /**
     * Pop an item off the action stack
     * 
     * @return false| ControllerRequestAbstract 
     */
    public function popStack()
    {
        $stack = $this->getStack();
        if (0 == count($stack)) {
            return false;
        }

        $next = array_pop($stack);
        $this->_saveStack($stack);

        if (!$next instanceof  ControllerRequestAbstract ) {
            require_once 'Zend/Controller/Exception.php';
            throw new  ControllerException ('ArrayStack should only contain request objects');
        }
        $action = $next->getActionName();
        if (empty($action)) {
            return $this->popStack($stack);
        }

        $request    = $this->getRequest();
        $controller = $next->getControllerName();
        if (empty($controller)) {
            $next->setControllerName($request->getControllerName());
        }

        $module = $next->getModuleName();
        if (empty($module)) {
            $next->setModuleName($request->getModuleName());
        }

        return $next;
    }

    /**
     * postDispatch() plugin hook -- check \for actions in stack, and dispatch if any found
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function postDispatch( ControllerRequestAbstract  $request)
    {
        // Don't move on to next request if this is already an attempt to 
        // forward
        if (!$request->isDispatched()) {
            return;
        }

        $this->setRequest($request);
        $stack = $this->getStack();
        if (empty($stack)) {
            return;
        }
        $next = $this->popStack();
        if (!$next) {
            return;
        }

        $this->forward($next);
    }

    /**
     * Forward request with next action
     * 
     * @param  array $next 
     * @return void
     */
    public function forward( ControllerRequestAbstract  $next)
    {
        $this->getRequest()->setModuleName($next->getModuleName())
                           ->setControllerName($next->getControllerName())
                           ->setActionName($next->getActionName())
                           ->setParams($next->getParams())
                           ->setDispatched(false);
    }
}
