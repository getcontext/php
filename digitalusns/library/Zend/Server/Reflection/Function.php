<?php

namespace Zend\Server\Reflection;


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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 *  ServerReflectionFunctionAbstract 
 */
require_once 'Zend/Server/Reflection/Function/Abstract.php';

/**
 * Function Reflection
 *
 * @uses        ServerReflectionFunctionAbstract 
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Reflection
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id: Function.php 8064 2008-02-16 10:58:39Z thomas $
 */


use Zend\Server\Reflection\Function\FunctionAbstract as ServerReflectionFunctionAbstract;




class  ReflectionFunction  extends  ServerReflectionFunctionAbstract 
{
}
