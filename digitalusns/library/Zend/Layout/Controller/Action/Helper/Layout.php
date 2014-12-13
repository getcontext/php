<?php

namespace Zend\Layout\Controller\Action\Helper;


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
 * @subpackage \Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Layout.php 9098 2008-03-30 19:29:10Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  ControllerActionHelperAbstract  */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Helper \for interacting with  ZendLayout  objects
 *
 * @uses        ControllerActionHelperAbstract 
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage \Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Controller\Action\Helper\HelperAbstract as ControllerActionHelperAbstract;
use Zend\Controller\Front as Front;
use Zend\Layout\Exception as LayoutException;
use Zend\Layout as ZendLayout;




class  Layout  extends  ControllerActionHelperAbstract 
{
    /**
     * @var  Front 
     */
    protected $_frontController;

    /**
     * @var  ZendLayout 
     */
    protected $_layout;

    /**
     * @var bool
     */
    protected $_isActionControllerSuccessful = false;
    
    /**
     * Constructor
     * 
     * @param   ZendLayout  $layout 
     * @return void
     */
    public function __construct( ZendLayout  $layout = null)
    {
        if (null !== $layout) {
            $this->setLayoutInstance($layout);
        } else {
            $layout =  ZendLayout ::getMvcInstance();
        }
        
        if (null !== $layout) {
            $pluginClass = $layout->getPluginClass();
            $front = $this->getFrontController();
            if ($front->hasPlugin($pluginClass)) {
                $plugin = $front->getPlugin($pluginClass);
                $plugin->setLayoutActionHelper($this);
            }
        }
    }

    public function init()
    {
        $this->_isActionControllerSuccessful = false;
    }

    /**
     * Get front controller instance
     * 
     * @return  Front 
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            require_once 'Zend/Controller/Front.php';
            $this->_frontController =  Front ::getInstance();
        }

        return $this->_frontController;
    }
    
    /**
     * Get layout object
     * 
     * @return  ZendLayout 
     */
    public function getLayoutInstance()
    {
        if (null === $this->_layout) {
            require_once 'Zend/Layout.php';
            if (null === ($this->_layout =  ZendLayout ::getMvcInstance())) {
                $this->_layout = new  ZendLayout ();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     * 
     * @param   ZendLayout  $layout 
     * @return  Layout 
     */
    public function setLayoutInstance( ZendLayout  $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Mark Action Controller (according to this plugin) as Running successfully
     *
     * @return  Layout 
     */
    public function postDispatch()
    {
        $this->_isActionControllerSuccessful = true;
        return $this;
    }
    
    /**
     * Did the previous action successfully complete?
     *
     * @return bool
     */
    public function isActionControllerSuccessful()
    {
        return $this->_isActionControllerSuccessful;
    }
    
    /**
     * Strategy pattern; call object as method
     *
     * Returns layout object
     * 
     * @return  ZendLayout 
     */
    public function direct()
    {
        return $this->getLayoutInstance();
    }

    /**
     * Proxy method calls to layout object
     * 
     * @param  string $method 
     * @param  array $args 
     * @return mixed
     */
    public function __call($method, $args)
    {
        $layout = $this->getLayoutInstance();
        if (method_exists($layout, $method)) {
            return call_user_func_array(array($layout, $method), $args);
        }

        require_once 'Zend/Layout/Exception.php';
        throw new  LayoutException (sprintf("Invalid method '%s' called on layout action helper", $method));
    }
}
