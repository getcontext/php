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
 * PDF file 'dictionary' element implementation
 *
 * @category   Zend
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\Element as Element;
use Zend\Pdf\Factory as Factory;




class  Dictionary  extends  Element 
{
    /**
     * Dictionary elements
     * Array \of  Element  objects ('name' =>  Element )
     *
     * @var array
     */
    private $_items = array();


    /**
     * Object constructor
     *
     * @param array $val   - array \of  Element  objects
     * @throws  PdfException 
     */
    public function __construct($val = null)
    {
        if ($val === null) {
            return;
        } else if (!is_array($val)) {
            throw new  PdfException ('Argument must be an array');
        }

        foreach ($val as $name => $element) {
            if (!$element instanceof  Element ) {
                throw new  PdfException ('Array elements must be  Element  objects');
            }
            if (!is_string($name)) {
                throw new  PdfException ('Array keys must be strings');
            }
            $this->_items[$name] = $element;
        }
    }


    /**
     * Add element to an array
     *
     * @name  Name  $name
     * @param  Element  $val   -  Element  object
     * @throws  PdfException 
     */
    public function add( Name  $name,  Element  $val)
    {
        $this->_items[$name->value] = $val;
    }

    /**
     * Return dictionary keys
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->_items);
    }


    /**
     * Get handler
     *
     * @param string $property
     * @return  Element  | null
     */
    public function __get($item)
    {
        $element = isset($this->_items[$item]) ? $this->_items[$item]
                                               : null;

        return $element;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($item, $value)
    {
        if ($value === null) {
            unset($this->_items[$item]);
        } else {
            $this->_items[$item] = $value;
        }
    }

    /**
     * Return type \of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return  Element ::TYPE_DICTIONARY;
    }


    /**
     * Return object as string
     *
     * @param  Factory  $factory
     * @return string
     */
    public function toString($factory = null)
    {
        $outStr = '<<';
        $lastNL = 0;

        foreach ($this->_items as $name => $element) {
            if (!is_object($element)) {
                throw new  PdfException ('Wrong data');
            }

            if (strlen($outStr) - $lastNL > 128)  {
                $outStr .= "\n";
                $lastNL = strlen($outStr);
            }

            $nameObj = new  Name ($name);
            $outStr .= $nameObj->toString($factory) . ' ' . $element->toString($factory) . ' ';
        }
        $outStr .= '>>';

        return $outStr;
    }
}
