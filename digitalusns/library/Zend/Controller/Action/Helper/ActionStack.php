<?php

namespace Zend\Controller\Action\Helper;


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
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: ActionStack.php 9098 2008-03-30 19:29:10Z thomas $
 */

/**
 * @see  ControllerActionHelperAbstract 
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * @see \Zend\Registry
 */
require_once 'Zend/Registry.php';

/**
 * Add to action stack
 *
 * @uses        ControllerActionHelperAbstract 
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Controller\Action\Helper\HelperAbstract as ControllerActionHelperAbstract;
use Zend\Controller\Plugin\ActionStack as ControllerPluginActionStack;
use Zend\Controller\Request\RequestAbstract as ControllerRequestAbstract;
use Zend\Controller\Action\Exception as ControllerActionException;
use Zend\Controller\Request\Simple as Simple;
use Zend\Controller\Front as Front;




class  ActionStack  extends  ControllerActionHelperAbstract 
{
    /**
     * @var  ControllerPluginActionStack 
     */
    protected $_actionStack;

    /**
     * Constructor
     *
     * Register action stack plugin
     * 
     * @return void
     */
    public function __construct()
    {
        $front =  Front ::getInstance();
        if (!$front->hasPlugin('\Zend\Controller\Plugin\ActionStack')) {
            /**
             * @see  ControllerPluginActionStack 
             */
            require_once 'Zend/Controller/Plugin/ActionStack.php';
            $this->_actionStack = new  ControllerPluginActionStack ();
            $front->registerPlugin($this->_actionStack, 97);
        } else {
            $this->_actionStack = $front->getPlugin('\Zend\Controller\Plugin\ActionStack');
        }
    }

    /**
     * Push onto the stack 
     * 
     * @param   ControllerRequestAbstract  $next 
     * @return  ActionStack  Provides a fluent interface
     */
    public function pushStack( ControllerRequestAbstract  $next)
    {
        $this->_actionStack->pushStack($next);
        return $this;
    }

    /**
     * Push a new action onto the stack
     * 
     * @param  string $action 
     * @param  string $controller 
     * @param  string $module 
     * @param  array  $params
     * @throws  ControllerActionException  
     * @return  ActionStack 
     */
    public function actionToStack($action, $controller = null, $module = null, array $params = array())
    {
        if ($action instanceof  ControllerRequestAbstract ) {
            return $this->pushStack($action);
        } elseif (!is_string($action)) {
            /**
             * @see  ControllerActionException 
             */
            require_once 'Zend/Controller/Action/Exception.php';
            throw new  ControllerActionException ('ActionStack requires either a request object or minimally a string action');
        }

        $request = $this->getRequest();

        if ($request instanceof  ControllerRequestAbstract  === false){
            /**
             * @see  ControllerActionException 
             */
            require_once 'Zend/Controller/Action/Exception.php';
            throw new  ControllerActionException ('Request object not set yet');
        }
        
        $controller = (null === $controller) ? $request->getControllerName() : $controller;
        $module = (null === $module) ? $request->getModuleName() : $module;

        /**
         * @see  Simple 
         */
        require_once 'Zend/Controller/Request/Simple.php';
        $newRequest = new  Simple ($action, $controller, $module, $params);

        return $this->pushStack($newRequest);
    }

    /**
     * Perform helper when called as $this->_helper->actionStack() from an action controller
     *
     * Proxies to {@link simple()}
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array $params
     * @return boolean
     */
    public function direct($action, $controller = null, $module = null, array $params = array())
    {
        return $this->actionToStack($action, $controller, $module, $params);
    }
}
