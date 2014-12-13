<?php

namespace Zend\Pdf;


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
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Element  */
require_once 'Zend/Pdf/Element.php';

/**  Object  */
require_once 'Zend/Pdf/Element/Object.php';


/**
 * PDF file element implementation
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Pdf\Element\Object as Object;
use Zend\Pdf\Factory as Factory;



abstract class  Element 
{
    const TYPE_BOOL        = 1;
    const TYPE_NUMERIC     = 2;
    const TYPE_STRING      = 3;
    const TYPE_NAME        = 4;
    const TYPE_ARRAY       = 5;
    const TYPE_DICTIONARY  = 6;
    const TYPE_STREAM      = 7;
    const TYPE_NULL        = 11;

    /**
     * \Reference to the top level indirect object, which contains this element.
     *
     * @var  Object 
     */
    private $_parentObject = null;

    /**
     * Return type \of the element.
     * See ZPdfPDFConst \for possible values
     *
     * @return integer
     */
    abstract public function getType();

    /**
     * Convert element to a string, which can be directly
     * written to a PDF file.
     *
     * $factory parameter defines operation context.
     *
     * @param  Factory  $factory
     * @return string
     */
    abstract public function toString($factory = null);


    /**
     * Set top level parent indirect object.
     *
     * @param  Object  $parent
     */
    public function setParentObject( Object  &$parent)
    {
        $this->_parentObject = &$parent;
    }


    /**
     * Get top level parent indirect object.
     *
     * @return  Object 
     */
    public function getParentObject()
    {
        return $this->_parentObject;
    }


    /**
     * Mark object as modified, to include it into new PDF file segment.
     *
     * We don't automate this action to keep control on PDF update process.
     * All new objects are treated as "modified" automatically.
     */
    public function touch()
    {
        if ($this->_parentObject !== null) {
            $this->_parentObject->touch();
        }
    }

    /**
     * Clean up resources, used by object
     */
    public function cleanUp()
    {
        // Do nothing
    }
}

