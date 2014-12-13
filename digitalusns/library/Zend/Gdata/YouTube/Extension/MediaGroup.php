<?php

namespace Zend\Gdata\YouTube\Extension;



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
 * @see  GdataMediaExtensionMediaGroup 
 */
require_once 'Zend/Gdata/Media/Extension/MediaGroup.php';

/**
 * @see  MediaContent 
 */
require_once 'Zend/Gdata/YouTube/Extension/MediaContent.php';

/**
 * @see  Duration 
 */
require_once 'Zend/Gdata/YouTube/Extension/Duration.php';

/**
 * @see  GdataYouTubeExtensionPrivate 
 */
require_once 'Zend/Gdata/YouTube/Extension/Private.php';

/**
 * This class represents the media:group element \of Media RSS.
 * It allows the grouping \of media:content elements that are 
 * different representations \of the same content.  When it exists,
 * it is a child \of an Entry (Atom) or Item (RSS).
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\YouTube\Extension\MediaContent as MediaContent;
use Zend\Gdata\Media\Extension\MediaGroup as GdataMediaExtensionMediaGroup;
use Zend\Gdata\YouTube\Extension\Duration as Duration;
use Zend\Gdata\YouTube\Extension\ExtensionPrivate as GdataYouTubeExtensionPrivate;
use Zend\Gdata\YouTube as YouTube;




class  MediaGroup  extends  GdataMediaExtensionMediaGroup 
{

    protected $_rootElement = 'group';
    protected $_rootNamespace = 'media';

    protected $_duration = null;
    protected $_private = null;

    public function __construct($element = null)
    {
        foreach ( YouTube ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_duration !== null) {
            $element->appendChild($this->_duration->getDOM($element->ownerDocument));
        }
        if ($this->_private !== null) {
            $element->appendChild($this->_private->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects \of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('media') . ':' . 'content':
                $content = new  MediaContent ();
                $content->transferFromDOM($child);
                $this->_content[] = $content;
                break;
            case $this->lookupNamespace('yt') . ':' . 'duration':
                $duration = new  Duration ();
                $duration->transferFromDOM($child);
                $this->_duration = $duration;
                break;
            case $this->lookupNamespace('yt') . ':' . 'private':
                $private = new  GdataYouTubeExtensionPrivate ();
                $private->transferFromDOM($child);
                $this->_private = $private;
                break;

        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Returns the duration value \of this element
     *
     * @return  Duration 
     */
    public function getDuration()
    {
        return $this->_duration;
    }

    /**
     * Sets the duration value \of this element
     *
     * @param  Duration  $value The duration value
     * @return  MediaGroup  Provides a fluent interface
     */
    public function setDuration($value)
    {
        $this->_duration = $value;
        return $this;
    }

    /**
     * Returns the private value \of this element
     *
     * @return  GdataYouTubeExtensionPrivate 
     */
    public function getPrivate()
    {
        return $this->_private;
    }

    /**
     * Sets the private value \of this element
     *
     * @param  GdataYouTubeExtensionPrivate  $value The private value
     * @return  MediaGroup  Provides a fluent interface
     */
    public function setPrivate($value)
    {
        $this->_private = $value;
        return $this;
    }

}
