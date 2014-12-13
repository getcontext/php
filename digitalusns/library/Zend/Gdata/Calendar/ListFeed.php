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
 * @see  Feed 
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * @see \Zend\Gdata\Extension_Timezone
 */
require_once 'Zend/Gdata/Calendar/Extension/Timezone.php';

/**
 * Represents the meta-feed list \of calendars
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Calendar\Extension\Timezone as Timezone;
use Zend\Gdata\Calendar as Calendar;
use Zend\Gdata\Feed as Feed;




class  ListFeed  extends  Feed 
{
    protected $_timezone = null;

    /**
     * The classname \for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\Calendar\ListEntry';

    /**
     * The classname \for the feed.
     *
     * @var string
     */
    protected $_feedClassName = '\Zend\Gdata\Calendar\ListFeed';

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
        if ($this->_timezone != null) {
            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gCal') . ':' . 'timezone';
            $timezone = new  Timezone ();
            $timezone->transferFromDOM($child);
            $this->_timezone = $timezone;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
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

}
