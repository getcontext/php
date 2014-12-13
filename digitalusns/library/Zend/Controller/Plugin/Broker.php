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


/**  ControllerException  */
require_once 'Zend/Controller/Exception.php';

/** \Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**  ControllerRequestAbstract  */
require_once 'Zend/Controller/Request/Abstract.php';

/**  ControllerResponseAbstract  */
require_once 'Zend/Controller/Response/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Controller\Response\ResponseAbstract as ControllerResponseAbstract;
use Zend\Controller\Request\RequestAbstract as ControllerRequestAbstract;
use Zend\Controller\Exception as ControllerException;
use Zend\Controller\Front as Front;




class  Broker  extends \Zend_Controller_Plugin_Abstract
{

    /**
     * Array \of instance \of objects extending \Zend_Controller_Plugin_Abstract
     *
     * @var array
     */
    protected $_plugins = array();


    /**
     * Register a plugin.
     *
     * @param  \Zend_Controller_Plugin_Abstract $plugin
     * @param  int $stackIndex
     * @return  Broker 
     */
    public function registerPlugin(\Zend_Controller_Plugin_Abstract $plugin, $stackIndex = null)
    {
        if (false !== array_search($plugin, $this->_plugins, true)) {
            throw new  ControllerException ('Plugin already registered');
        }

        $stackIndex = (int) $stackIndex;

        if ($stackIndex) {
            if (isset($this->_plugins[$stackIndex])) {
                throw new  ControllerException ('Plugin with stackIndex "' . $stackIndex . '" already registered');
            }
            $this->_plugins[$stackIndex] = $plugin;
        } else {
            $stackIndex = count($this->_plugins);
            while (isset($this->_plugins[$stackIndex])) {
                ++$stackIndex;
            }
            $this->_plugins[$stackIndex] = $plugin;
        }

        $request = $this->getRequest();
        if ($request) {
            $this->_plugins[$stackIndex]->setRequest($request);
        }
        $response = $this->getResponse();
        if ($response) {
            $this->_plugins[$stackIndex]->setResponse($response);
        }

        ksort($this->_plugins);

        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param string|\Zend_Controller_Plugin_Abstract $plugin Plugin object or class name
     * @return  Broker 
     */
    public function unregisterPlugin($plugin)
    {
        if ($plugin instanceof \Zend_Controller_Plugin_Abstract) {
            // Given a plugin object, find it in the array
            $key = array_search($plugin, $this->_plugins, true);
            if (false === $key) {
                throw new  ControllerException ('Plugin never registered.');
            }
            unset($this->_plugins[$key]);
        } elseif (is_string($plugin)) {
            // Given a plugin class, find all plugins \of that class and unset them
            foreach ($this->_plugins as $key => $_plugin) {
                $type = get_class($_plugin);
                if ($plugin == $type) {
                    unset($this->_plugins[$key]);
                }
            }
        }
        return $this;
    }

    /**
     * Is a plugin \of a particular class registered?
     *
     * @param  string $class
     * @return bool
     */
    public function hasPlugin($class)
    {
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class Class name \of plugin(s) desired
     * @return false|\Zend_Controller_Plugin_Abstract|array Returns false if none found, plugin if only one found, and array \of plugins if multiple plugins \of same class found
     */
    public function getPlugin($class)
    {
        $found = array();
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                return false;
            case 1:
                return $found[0];
            default:
                return $found;
        }
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Set request object, and register with each plugin
     *
     * @param  ControllerRequestAbstract  $request
     * @return  Broker 
     */
    public function setRequest( ControllerRequestAbstract  $request)
    {
        $this->_request = $request;

        foreach ($this->_plugins as $plugin) {
            $plugin->setRequest($request);
        }

        return $this;
    }

    /**
     * Get request object
     *
     * @return  ControllerRequestAbstract  $request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object
     *
     * @param  ControllerResponseAbstract  $response
     * @return  Broker 
     */
    public function setResponse( ControllerResponseAbstract  $response)
    {
        $this->_response = $response;

        foreach ($this->_plugins as $plugin) {
            $plugin->setResponse($response);
        }


        return $this;
    }

    /**
     * Get response object
     *
     * @return  ControllerResponseAbstract  $response
     */
    public function getResponse()
    {
        return $this->_response;
    }


    /**
     * Called before  Front  begins evaluating the
     * request against its routes.
     *
     * @param  ControllerRequestAbstract  $request
     * @return void
     */
    public function routeStartup( ControllerRequestAbstract  $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->routeStartup($request);
            } catch (\Exception $e) {
                if ( Front ::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before  Front  exits its iterations over
     * the route set.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function routeShutdown( ControllerRequestAbstract  $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->routeShutdown($request);
            } catch (\Exception $e) {
                if ( Front ::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before  Front  enters its dispatch loop.
     *
     * During the dispatch loop,  Front  keeps a
     *  ControllerRequestAbstract  object, and uses
     * Zend_Controller_Dispatcher to dispatch the
     *  ControllerRequestAbstract  object to controllers/actions.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function dispatchLoopStartup( ControllerRequestAbstract  $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->dispatchLoopStartup($request);
            } catch (\Exception $e) {
                if ( Front ::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function preDispatch( ControllerRequestAbstract  $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->preDispatch($request);
            } catch (\Exception $e) {
                if ( Front ::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function postDispatch( ControllerRequestAbstract  $request)
    {
        foreach ($this->_plugins as $plugin) {
            try {
                $plugin->postDispatch($request);
            } catch (\Exception $e) {
                if ( Front ::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
        }
    }


    /**
     * Called before  Front  exits its dispatch loop.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function dispatchLoopShutdown()
    {
       foreach ($this->_plugins as $plugin) {
           try {
                $plugin->dispatchLoopShutdown();
            } catch (\Exception $e) {
                if ( Front ::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->getResponse()->setException($e);
                }
            }
       }
    }
}
