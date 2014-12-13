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
 *  ServerReflectionException 
 */
require_once 'Zend/Server/Reflection/Exception.php';

/**
 *  ReturnValue 
 */
require_once 'Zend/Server/Reflection/ReturnValue.php';

/**
 *  Parameter 
 */
require_once 'Zend/Server/Reflection/Parameter.php';

/**
 * Method/Function prototypes
 *
 * Contains accessors \for the return value and all method arguments.
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Reflection
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id: Prototype.php 8064 2008-02-16 10:58:39Z thomas $
 */


use Zend\Server\Reflection\ReturnValue as ReturnValue;
use Zend\Server\Reflection\Parameter as Parameter;
use Zend\Server\Reflection\Exception as ServerReflectionException;




class  Prototype 
{
    /**
     * Constructor
     *
     * @param  ReturnValue  $return
     * @param array $params
     * @return void
     */
    public function __construct( ReturnValue  $return, $params = null)
    {
        $this->_return = $return;

        if (!is_array($params) && (null !== $params)) {
            throw new  ServerReflectionException ('Invalid parameters');
        }

        if (is_array($params)) {
            foreach ($params as $param) {
                if (!$param instanceof  Parameter ) {
                    throw new  ServerReflectionException ('One or more params are invalid');
                }
            }
        }

        $this->_params = $params;
    }

    /**
     * Retrieve return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->_return->getType();
    }

    /**
     * Retrieve the return value object
     *
     * @access public
     * @return  ReturnValue 
     */
    public function getReturnValue()
    {
        return $this->_return;
    }

    /**
     * Retrieve method parameters
     *
     * @return array Array \of {@link  Parameter }s
     */
    public function getParameters()
    {
        return $this->_params;
    }
}
