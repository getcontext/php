<?php

namespace Zend\Memory;


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
 * @package    \Zend\Memory
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** \Zend\Memory\Exception */
require_once 'Zend/Memory/Exception.php';

/**  MemoryContainerInterface  */
require_once 'Zend/Memory/Container/Interface.php';


/**
 * Memory value container
 *
 * @category   Zend
 * @package    \Zend\Memory
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Memory\Container\ContainerInterface as MemoryContainerInterface;



abstract class  Container  implements  MemoryContainerInterface 
{
}
