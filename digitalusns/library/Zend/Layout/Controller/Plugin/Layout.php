<?php

namespace Zend\Layout\Controller\Plugin;


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

/**
 * Render layouts
 *
 * @uses       \Zend_Controller_Plugin_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Layout.php 8064 2008-02-16 10:58:39Z thomas $
 */


use Zend\Layout\Controller\Action\Helper\Layout as LayoutControllerActionHelperLayout;
use Zend\Controller\Request\RequestAbstract as ControllerRequestAbstract;
use Zend\Layout as ZendLayout;




class  Layout  extends \Zend_Controller_Plugin_Abstract
{
    protected $_layoutActionHelper = null;
    
    /**
     * @var  ZendLayout 
     */
    protected $_layout;

    /**
     * Constructor
     * 
     * @param   ZendLayout  $layout 
     * @return void
     */
    public function __construct( ZendLayout  $layout = null)
    {
        if (null !== $layout) {
            $this->setLayout($layout);
        }
    }

    /**
     * Retrieve layout object
     *
     * @return  ZendLayout 
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param   ZendLayout  $layout
     * @return  Layout 
     */
    public function setLayout( ZendLayout  $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Set layout action helper
     * 
     * @param   LayoutControllerActionHelperLayout  $layoutActionHelper 
     * @return  Layout 
     */
    public function setLayoutActionHelper( LayoutControllerActionHelperLayout  $layoutActionHelper)
    {
        $this->_layoutActionHelper = $layoutActionHelper;
        return $this;
    }

    /**
     * Retrieve layout action helper
     * 
     * @return  LayoutControllerActionHelperLayout 
     */
    public function getLayoutActionHelper()
    {
        return $this->_layoutActionHelper;
    }
    
    /**
     * postDispatch() plugin hook -- render layout
     *
     * @param   ControllerRequestAbstract  $request
     * @return void
     */
    public function postDispatch( ControllerRequestAbstract  $request)
    {
        $layout = $this->getLayout();
        $helper = $this->getLayoutActionHelper();

        // Return early if forward detected
        if (!$request->isDispatched() 
            || ($layout->getMvcSuccessfulActionOnly() 
                && (!empty($helper) && !$helper->isActionControllerSuccessful()))) 
        {
            return;
        }

        // Return early if layout has been disabled
        if (!$layout->isEnabled()) {
            return;
        }

        $response   = $this->getResponse();
        $content    = $response->getBody(true);
        $contentKey = $layout->getContentKey();

        if (isset($content['default'])) {
            $content[$contentKey] = $content['default'];
        }
        if ('default' != $contentKey) {
            unset($content['default']);
        }

        $layout->assign($content);
        
        $fullContent = null;
        $obStartLevel = ob_get_level();
        try {
            $fullContent = $layout->render();
            $response->setBody($fullContent);
        } catch (\Exception $e) {
            while (ob_get_level() > $obStartLevel) {
                $fullContent .= ob_get_clean();
            }
            $request->setParam('layoutFullContent', $fullContent);
            $request->setParam('layoutContent', $layout->content);
            $response->setBody(null);
            throw $e;
        }

    }
}
