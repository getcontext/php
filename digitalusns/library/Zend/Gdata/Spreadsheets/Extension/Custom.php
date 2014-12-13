<?php

namespace Zend\Gdata\Spreadsheets\Extension;


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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Entry.php 3941 2007-03-14 21:36:13Z darby $
 */

/**
 * @see \Zend\Gdata\Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see  Extension 
 */
require_once 'Zend/Gdata/Extension.php';


/**
 * Concrete class \for working with custom gsx elements.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Spreadsheets as Spreadsheets;
use Zend\Gdata\Extension as Extension;




class  Custom  extends  Extension 
{
    // custom elements have custom names.
    protected $_rootElement = null; // The name \of the column
    protected $_rootNamespace = 'gsx';

    /**
     * Constructs a new  Custom  object.
     * @param string $column (optional) The column/tag name \of the element.
     * @param string $value (optional) The text content \of the element.
     */
    public function __construct($column = null, $value = null)
    {
        foreach ( Spreadsheets ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->_text = $value;
        $this->_rootElement = $column;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        return $element;
    }

    /**
     * Transfers each child and attribute into member variables.
     * This is called when XML is received over the wire and the data
     * model needs to be built to represent this XML.
     *
     * @param DOMNode $node The DOMNode that represents this object's data
     */
    public function transferFromDOM($node)
    {
        parent::transferFromDOM($node);
        $this->_rootElement = $node->localName;
    }

    /**
     * Sets the column/tag name \of the element.
     * @param string $column The new column name.
     */
    public function setColumnName($column)
    {
        $this->_rootElement = $column;
        return $this;
    }

    /**
     * Gets the column name \of the element
     * @return string The column name.
     */
    public function getColumnName()
    {
        return $this->_rootElement;
    }

}
