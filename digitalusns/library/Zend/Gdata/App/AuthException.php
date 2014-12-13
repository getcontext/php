<?php

namespace Zend\Gdata\App;


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
 * @package    \Zend\Gdata
 * @subpackage \Zend\Gdata\App
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  GdataAppException 
 */
require_once 'Zend/Gdata/App/Exception.php';

/**
 * Gdata exceptions
 *
 * Class to represent exceptions that occur during Gdata operations.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @subpackage \Zend\Gdata\App
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\Exception as GdataAppException;




class  AuthException  extends  GdataAppException 
{
}
