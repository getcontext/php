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

/**  PhpArray  */
require_once 'Zend/Pdf/PhpArray.php';



/**
 * PDF file 'array' element implementation
 *
 * @category   Zend
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\PhpArray as PhpArray;
use Zend\Pdf\Element as Element;
use Zend\Pdf\Factory as Factory;
use unknown\type as type;




class  ElementArray  extends  Element 
{
    /**
     * Object value
     * Array \of  Element  objects.
     * Appropriate methods must (!) be used to modify it to provide correct
     * work with objects and references.
     *
     * @var  PhpArray 
     */
    private $_items;


    /**
     * Object constructor
     *
     * @param array $val   - array \of  Element  objects
     * @throws  PdfException 
     */
    public function __construct($val = null)
    {
        $this->_items = new  PhpArray ();

        if ($val !== null  &&  is_array($val)) {
            foreach ($val as $element) {
                if (!$element instanceof  Element ) {
                    throw new  PdfException ('Array elements must be  Element  objects');
                }
                $this->_items[] = $element;
            }
        } else if ($val !== null){
            throw new  PdfException ('Argument must be an array');
        }
    }


    /**
     * Provides access to $this->_items
     *
     * @param string $property
     * @return  PhpArray 
     */
    public function __get($property) {
        if ($property=='items') {
            return $this->_items;
        }
        throw new \Exception('Undefined property:  ElementArray ::$' . $property);
    }


    /**
     * Provides read-only access to $this->_items;
     *
     * @param  type  $offset
     * @param  type  $value
     */
    public function __set($property, $value) {
        if ($property=='items') {
            throw new \Exception('Array container cannot be overwritten');
        }
        throw new \Exception('Undefined property:  ElementArray ::$' . $property);
    }

    /**
     * Return type \of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return  Element ::TYPE_ARRAY;
    }


    /**
     * Return object as string
     *
     * @param  Factory  $factory
     * @return string
     */
    public function toString($factory = null)
    {
        $outStr = '[';
        $lastNL = 0;

        foreach ($this->_items as $element) {
            if (strlen($outStr) - $lastNL > 128)  {
                $outStr .= "\n";
                $lastNL = strlen($outStr);
            }

            $outStr .= $element->toString($factory) . ' ';
        }
        $outStr .= ']';

        return $outStr;
    }
}
