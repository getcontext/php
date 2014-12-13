<?php

namespace Zend\Dojo;


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
 * @package    \Zend\Dojo
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  ZendForm  */
require_once 'Zend/Form.php';

/**
 * Dijit-enabled Form
 * 
 * @uses        ZendForm 
 * @package    \Zend\Dojo
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Form.php 10076 2008-07-13 12:58:08Z matthew $
 */


use Zend\View\ViewInterface as ViewInterface;
use Zend\Form as ZendForm;




class  Form  extends  ZendForm 
{
    /**
     * Constructor
     * 
     * @param  array|\Zend\Config|null $options 
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addPrefixPath('\Zend\Dojo\Form_Element', 'Zend/Dojo/Form/Element', 'element')
             ->addElementPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator')
             ->setDefaultDisplayGroupClass('\Zend\Dojo\Form\DisplayGroup');
        parent::__construct($options);
    }

    /**
     * Load the default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form'))
                 ->addDecorator('DijitForm');
        }
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the dojo view helper path set.
     * 
     * @param   ViewInterface  $view 
     * @return  Form _Element_Dijit
     */
    public function setView( ViewInterface  $view = null)
    {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('\Zend\Dojo_View_Helper')) {
                $view->addHelperPath('Zend/Dojo/View/Helper', '\Zend\Dojo_View_Helper');
            }
        }
        return parent::setView($view);
    }
}
