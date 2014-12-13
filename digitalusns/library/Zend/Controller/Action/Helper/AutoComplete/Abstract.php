<?php

namespace Zend\Controller\Action\Helper\AutoComplete;


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
 * @version    $Id: Abstract.php 9098 2008-03-30 19:29:10Z thomas $
 */

/**
 * @see  ControllerActionHelperAbstract 
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Create and send autocompletion lists
 *
 * @uses        ControllerActionHelperAbstract 
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Controller\Action\Helper\HelperAbstract as ControllerActionHelperAbstract;
use Zend\Controller\Action\HelperBroker as HelperBroker;
use Zend\Controller\Action\Exception as ControllerActionException;
use Zend\Layout as Layout;



abstract class  AutoCompleteAbstract  extends  ControllerActionHelperAbstract 
{
    /**
     * Suppress exit when sendJson() called
     *
     * @var boolean
     */
    public $suppressExit = false;

    /**
     * Validate autocompletion data
     * 
     * @param  mixed $data
     * @return boolean
     */
    abstract public function validateData($data);

    /**
     * Prepare autocompletion data
     * 
     * @param  mixed   $data 
     * @param  boolean $keepLayouts 
     * @return mixed
     */
    abstract public function prepareAutoCompletion($data, $keepLayouts = false);

    /**
     * Disable layouts and view renderer
     * 
     * @return  AutoCompleteAbstract  Provides a fluent interface
     */
    public function disableLayouts()
    {
        /**
         * @see  Layout 
         */
        require_once 'Zend/Layout.php';
        if (null !== ($layout =  Layout ::getMvcInstance())) {
            $layout->disableLayout();
        }

         HelperBroker ::getStaticHelper('viewRenderer')->setNoRender(true);

        return $this;
    }

    /**
     * Encode data to JSON
     * 
     * @param  mixed $data 
     * @param  bool  $keepLayouts 
     * @throws  ControllerActionException 
     * @return string
     */
    public function encodeJson($data, $keepLayouts = false)
    {
        if ($this->validateData($data)) {
            return  HelperBroker ::getStaticHelper('Json')->encodeJson($data, $keepLayouts);
        }

        /**
         * @see  ControllerActionException 
         */
        require_once 'Zend/Controller/Action/Exception.php';
        throw new  ControllerActionException ('Invalid data passed \for autocompletion');
    }

    /**
     * Send autocompletion data
     *
     * Calls prepareAutoCompletion, populates response body with this 
     * information, and sends response.
     * 
     * @param  mixed $data 
     * @param  bool  $keepLayouts 
     * @return string|void
     */
    public function sendAutoCompletion($data, $keepLayouts = false)
    {
        $data = $this->prepareAutoCompletion($data, $keepLayouts);

        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
            $response->sendResponse();
            exit;
        }

        return $data;
    }

    /**
     * Strategy pattern: allow calling helper as broker method
     *
     * Prepares autocompletion data and, if $sendNow is true, immediately sends 
     * response.
     * 
     * @param  mixed $data 
     * @param  bool  $sendNow 
     * @param  bool  $keepLayouts 
     * @return string|void
     */
    public function direct($data, $sendNow = true, $keepLayouts = false)
    {
        if ($sendNow) {
            return $this->sendAutoCompletion($data, $keepLayouts);
        }

        return $this->prepareAutoCompletion($data, $keepLayouts);
    }
}
