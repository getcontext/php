<?php

namespace Zend\Gdata\Calendar;



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
 * @see  Entry 
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Calendar_Extension_AccessLevel
 */
require_once 'Zend/Gdata/Calendar/Extension/AccessLevel.php';

/**
 * @see Zend_Calendar_Extension_Color
 */
require_once 'Zend/Gdata/Calendar/Extension/Color.php';

/**
 * @see Zend_Calendar_Extension_Hidden
 */
require_once 'Zend/Gdata/Calendar/Extension/Hidden.php';

/**
 * @see Zend_Calendar_Extension_Selected
 */
require_once 'Zend/Gdata/Calendar/Extension/Selected.php';

/**
 * @see \Zend\Gdata\Extension\EventStatus
 */
require_once 'Zend/Gdata/Extension/EventStatus.php';

/**
 * @see \Zend\Gdata\Extension\Visibility
 */
require_once 'Zend/Gdata/Extension/Visibility.php';


/**
 * @see Zend_Extension_Where
 */
require_once 'Zend/Gdata/Extension/Where.php';

/**
 * Represents a Calendar entry in the Calendar data API meta feed \of a user's
 * calendars.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Calendar\Extension\AccessLevel as AccessLevel;
use Zend\Gdata\Calendar\Extension\Selected as Selected;
use Zend\Gdata\Calendar\Extension\Timezone as Timezone;
use Zend\Gdata\Calendar\Extension\Hidden as Hidden;
use Zend\Gdata\Calendar\Extension\Color as Color;
use Zend\Gdata\Extension\Where as Where;
use Zend\Gdata\Calendar as Calendar;
use Zend\Gdata\Entry as Entry;




class  ListEntry  extends  Entry 
{

    protected $_color = null;
    protected $_accessLevel = null;
    protected $_hidden = null;
    protected $_selected = null;
    protected $_timezone = null;
    protected $_where = array();

    public function __construct($element = null)
    {
        foreach ( Calendar ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_accessLevel != null) {
            $element->appendChild($this->_accessLevel->getDOM($element->ownerDocument));
        }
        if ($this->_color != null) {
            $element->appendChild($this->_color->getDOM($element->ownerDocument));
        }
        if ($this->_hidden != null) {
            $element->appendChild($this->_hidden->getDOM($element->ownerDocument));
        }
        if ($this->_selected != null) {
            $element->appendChild($this->_selected->getDOM($element->ownerDocument));
        }
        if ($this->_timezone != null) {
            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
        }
        if ($this->_where != null) {
            foreach ($this->_where as $where) {
                $element->appendChild($where->getDOM($element->ownerDocument));
            }
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gCal') . ':' . 'accesslevel';
            $accessLevel = new  AccessLevel ();
            $accessLevel->transferFromDOM($child);
            $this->_accessLevel = $accessLevel;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'color';
            $color = new  Color ();
            $color->transferFromDOM($child);
            $this->_color = $color;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'hidden';
            $hidden = new  Hidden ();
            $hidden->transferFromDOM($child);
            $this->_hidden = $hidden;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'selected';
            $selected = new  Selected ();
            $selected->transferFromDOM($child);
            $this->_selected = $selected;
            break;
        case $this->lookupNamespace('gCal') . ':' . 'timezone';
            $timezone = new  Timezone ();
            $timezone->transferFromDOM($child);
            $this->_timezone = $timezone;
            break;
        case $this->lookupNamespace('gd') . ':' . 'where';
            $where = new  Where ();
            $where->transferFromDOM($child);
            $this->_where[] = $where;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    public function getAccessLevel()
    {
        return $this->_accessLevel;
    }

    /**
     * @param  AccessLevel  $value
     * @return \Zend\Gdata\Extension_ListEntry Provides a fluent interface
     */
    public function setAccessLevel($value)
    {
        $this->_accessLevel = $value;
        return $this;
    }
    public function getColor()
    {
        return $this->_color;
    }

    /**
     * @param  Color  $value
     * @return \Zend\Gdata\Extension_ListEntry Provides a fluent interface
     */
    public function setColor($value)
    {
        $this->_color = $value;
        return $this;
    }

    public function getHidden()
    {
        return $this->_hidden;
    }

    /**
     * @param  Hidden  $value
     * @return \Zend\Gdata\Extension_ListEntry Provides a fluent interface
     */
    public function setHidden($value)
    {
        $this->_hidden = $value;
        return $this;
    }

    public function getSelected()
    {
        return $this->_selected;
    }

    /**
     * @param  Selected  $value
     * @return \Zend\Gdata\Extension_ListEntry Provides a fluent interface
     */
    public function setSelected($value)
    {
        $this->_selected = $value;
        return $this;
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }

    /**
     * @param  Timezone  $value
     * @return \Zend\Gdata\Extension_ListEntry Provides a fluent interface
     */
    public function setTimezone($value)
    {
        $this->_timezone = $value;
        return $this;
    }

    public function getWhere()
    {
        return $this->_where;
    }

    /**
     * @param  Where  $value
     * @return \Zend\Gdata\Extension_ListEntry Provides a fluent interface
     */
    public function setWhere($value)
    {
        $this->_where = $value;
        return $this;
    }

}
