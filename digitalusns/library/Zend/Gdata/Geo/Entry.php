<?php

namespace Zend\Gdata\Geo;



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
 */

/**
 * @see  GdataEntry 
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see  Geo 
 */
require_once 'Zend/Gdata/Geo.php';

/**
 * @see  GeoRssWhere 
 */
require_once 'Zend/Gdata/Geo/Extension/GeoRssWhere.php';

/**
 * An Atom entry containing Geograpic data.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Geo\Extension\GeoRssWhere as GeoRssWhere;
use Zend\Gdata\Entry as GdataEntry;
use Zend\Gdata\Geo as Geo;




class  Entry  extends  GdataEntry 
{

    protected $_entryClassName = '\Zend\Gdata\Geo\Entry';

    protected $_where = null;

    public function __construct($element = null)
    {
        foreach ( Geo ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri); 
        }
        parent::__construct($element);
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_where != null) {
            $element->appendChild($this->_where->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('georss') . ':' . 'where':
            $where = new  GeoRssWhere ();
            $where->transferFromDOM($child);
            $this->_where = $where;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }
    
    public function getWhere()
    {
        return $this->_where;
    }

    public function setWhere($value)
    {
        $this->_where = $value;
        return $this;
    }
    

}
