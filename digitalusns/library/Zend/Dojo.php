<?php

namespace Zend;


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
 * @package     Dojo 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Enable Dojo components
 * 
 * @package     Dojo 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Dojo.php 10130 2008-07-16 14:51:08Z matthew $
 */


use Zend\View\ViewInterface as ViewInterface;
use Zend\Form as Form;




class  Dojo 
{
    /**
     *  @const string Base path to AOL CDN
     */
    const CDN_BASE_AOL = 'http://o.aolcdn.com/dojo/';

    /**
     * @const string Path to dojo on AOL CDN (following version string)
     */
    const CDN_DOJO_PATH_AOL = '/dojo/dojo.xd.js';

    /**
     *  @const string Base path to Google CDN
     */
    const CDN_BASE_GOOGLE = 'http://ajax.googleapis.com/ajax/libs/dojo/';

    /**
     * @const string Path to dojo on Google CDN (following version string)
     */
    const CDN_DOJO_PATH_GOOGLE = '/dojo/dojo.xd.js';

    /**
     * Dojo-enable a form instance
     * 
     * @param   Form  $form 
     * @return void
     */
    public static function enableForm( Form  $form)
    {
        $form->addPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addPrefixPath('\Zend\Dojo\Form_Element', 'Zend/Dojo/Form/Element', 'element')
             ->addElementPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('\Zend\Dojo\Form_Decorator', 'Zend/Dojo/Form/Decorator')
             ->setDefaultDisplayGroupClass('\Zend\Dojo\Form\DisplayGroup');

        foreach ($form->getSubForms() as $subForm) {
            self::enableForm($subForm);
        }

        if (null !== ($view = $form->getView())) {
            self::enableView($view);
        }
    }

    /**
     * Dojo-enable a view instance
     * 
     * @param   ViewInterface  $view 
     * @return void
     */
    public static function enableView( ViewInterface  $view)
    {
        if (false === $view->getPluginLoader('helper')->getPaths('\Zend\Dojo_View_Helper')) {
            $view->addHelperPath('Zend/Dojo/View/Helper', '\Zend\Dojo_View_Helper');
        }
    }
}

