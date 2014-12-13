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
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @see  Pdf 
 */
require_once 'Zend/Pdf.php';

/**
 * @see  Element 
 */
require_once 'Zend/Pdf/Element.php';

/**
 * @see \Zend\Memory
 */
require_once 'Zend/Memory.php';

/**
 * PDF file 'stream' element implementation
 *
 * @category   Zend
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element as Element;
use Zend\Pdf\Factory as Factory;
use Zend\Pdf as Pdf;




class  Stream  extends  Element 
{
    /**
     * Object value
     *
     * @var \Zend\Memory\Container
     */
    public $value;


    /**
     * Object constructor
     *
     * @param string $val
     */
    public function __construct($val)
    {
        $this->value =  Pdf ::getMemoryManager()->create($val);
    }


    /**
     * Return type \of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return  Element ::TYPE_STREAM;
    }


    /**
     * Stream length.
     * (Method is used to avoid string copying, which may occurs in some cases)
     *
     * @return integer
     */
    public function length()
    {
        return strlen($this->value->getRef());
    }


    /**
     * Clear stream
     *
     */
    public function clear()
    {
        $ref = &$this->value->getRef();
        $ref = '';
        $this->value->touch();
    }


    /**
     * Append value to a stream
     *
     * @param mixed $val
     */
    public function append($val)
    {
        $ref = &$this->value->getRef();
        $ref .= (string)$val;
        $this->value->touch();
    }


    /**
     * Return object as string
     *
     * @param  Factory  $factory
     * @return string
     */
    public function toString($factory = null)
    {
        return "stream\n" . $this->value->getRef() . "\nendstream";
    }
}
