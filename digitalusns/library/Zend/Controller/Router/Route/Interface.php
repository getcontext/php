<?php

namespace Zend\Controller\Router\Route;


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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Interface.php 10744 2008-08-07 02:32:44Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Config  */
require_once 'Zend/Config.php';

/**
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Config as Config;




interface  RouteInterface  {
    public function match($path);
    public function assemble($data = array(), $reset = false, $encode = false);
    public static function getInstance( Config  $config);
}

