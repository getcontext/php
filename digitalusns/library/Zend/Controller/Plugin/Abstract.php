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



abstract class \Zend_Controller_Plugin_Abstract
{
    /**
     * @var  ControllerRequestAbstract 
     */
    protected $_request;

    /**
     * @var  ControllerResponseAbstract 
     */
    protected $_response;

    /**
     * Set request object
     *
     * @param  ControllerRequestAbstract  $request
     * @return \Zend_Controller_Plugin_Abstract
     */
    public function setRequest( ControllerRequestAbstract  $request)
    {
        $this->_request = $request;
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
     * @return \Zend_Controller_Plugin_Abstract
     */
    public function setResponse( ControllerResponseAbstract  $response)
    {
        $this->_response = $response;
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
     * Called before \Zend\Controller\Front begins evaluating the
     * request against its routes.
     *
     * @param  ControllerRequestAbstract  $request
     * @return void
     */
    public function routeStartup( ControllerRequestAbstract  $request)
    {}

    /**
     * Called after Zend_Controller_Router exits.
     *
     * Called after \Zend\Controller\Front exits from the router.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function routeShutdown( ControllerRequestAbstract  $request)
    {}

    /**
     * Called before \Zend\Controller\Front enters its dispatch loop.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function dispatchLoopStartup( ControllerRequestAbstract  $request)
    {}

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows \for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link  ControllerRequestAbstract ::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function preDispatch( ControllerRequestAbstract  $request)
    {}

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows \for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag (via
     * {@link  ControllerRequestAbstract ::setDispatched() setDispatched(false)}),
     * a new action may be specified \for dispatching.
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function postDispatch( ControllerRequestAbstract  $request)
    {}

    /**
     * Called before \Zend\Controller\Front exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {}
}
