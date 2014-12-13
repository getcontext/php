<?php

namespace Zend\View\Helper;


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
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 10665 2008-08-05 10:57:18Z matthew $
 */

/**
 * @category   Zend
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\ViewInterface as ViewInterface;




interface  HelperInterface 
{
    /**
     * Set the \View object
     *
     * @param   ViewInterface  $view
     * @return  HelperInterface 
     */
    public function setView( ViewInterface  $view);

    /**
     * Strategy pattern: helper method to invoke
     * 
     * @return mixed
     */
    public function direct();
}
