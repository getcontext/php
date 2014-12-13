<?php

namespace Zend\Dojo\Form;


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

/**  FormDisplayGroup  */
require_once 'Zend/Form/DisplayGroup.php';

/**
 * Dijit-enabled DisplayGroup
 * 
 * @uses        FormDisplayGroup 
 * @package    \Zend\Dojo
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DisplayGroup.php 10076 2008-07-13 12:58:08Z matthew $
 */


use Zend\Loader\PluginLoader as PluginLoader;
use Zend\Form\DisplayGroup as FormDisplayGroup;
use Zend\View\ViewInterface as ViewInterface;




class  DisplayGroup  extends  FormDisplayGroup 
{
    /**
     * Constructor
     * 
     * @param  string $name
     * @param   PluginLoader  $loader
     * @param  array|\Zend\Config|null $options 
     * @return void
     */
    public function __construct($name,  PluginLoader  $loader, $options = null)
    {
        parent::__construct($name, $loader, $options);
        $this->addPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator');
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the dojo view helper path set.
     * 
     * @param   ViewInterface  $view 
     * @return \Zend\Dojo\Form\Element\Dijit
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
