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
 * PDF file 'boolean' element implementation
 *
 * @category   Zend
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\Element as Element;
use Zend\Pdf\Factory as Factory;




class  ElementBoolean  extends  Element 
{
    /**
     * Object value
     *
     * @var boolean
     */
    public $value;


    /**
     * Object constructor
     *
     * @param boolean $val
     * @throws  PdfException 
     */
    public function __construct($val)
    {
        if (! is_bool($val)) {
            throw new  PdfException ('Argument must be boolean.');
        }

        $this->value   = $val;
    }


    /**
     * Return type \of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return  Element ::TYPE_BOOL;
    }


    /**
     * Return object as string
     *
     * @param  Factory  $factory
     * @return string
     */
    public function toString($factory = null)
    {
        return $this->value ? 'true' : 'false';
    }
}
