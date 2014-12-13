<?php

namespace Zend\Pdf\Element;


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
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Element  */
require_once 'Zend/Pdf/Element.php';


/**
 * PDF file 'null' element implementation
 *
 * @category   Zend
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element as Element;
use Zend\Pdf\Factory as Factory;




class  Null  extends  Element 
{
    /**
     * Object value. Always null.
     *
     * @var mixed
     */
    public $value;


    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->value = null;
    }


    /**
     * Return type \of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return  Element ::TYPE_NULL;
    }


    /**
     * Return object as string
     *
     * @param  Factory  $factory
     * @return string
     */
    public function toString($factory = null)
    {
        return 'null';
    }
}
