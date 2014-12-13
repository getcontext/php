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
 * @see  Extension 
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * Represents the yt:duration element used by the YouTube data API
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Extension as Extension;
use Zend\Gdata\YouTube as YouTube;




class  Duration  extends  Extension 
{

    protected $_rootNamespace = 'yt';
    protected $_rootElement = 'duration';
    protected $_seconds = null;

    /**
     * Constructs a new  Duration  object.
     * @param bool $seconds(optional) The seconds value \of the element.
     */
    public function __construct($seconds = null) 
    {
        foreach ( YouTube ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();        
        $this->_seconds = $seconds; 
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all 
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text \for sending to the server upon updates, or
     * \for application storage/persistence.  
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all 
     * child properties.
     */
    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_seconds !== null) {
            $element->setAttribute('seconds', $this->_seconds);
        }
        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and valueare 
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'seconds':
            $this->_seconds = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value \for this element's seconds attribute.
     *
     * @return int The value associated with this attribute.
     */
    public function getSeconds()
    {
        return $this->_seconds;
    }

    /**
     * Set the value \for this element's seconds attribute.
     *
     * @param int $value The desired value \for this attribute.
     * @return  Duration  The element being modified.
     */
    public function setSeconds($value)
    {
        $this->_seconds = $value;
        return $this;
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     *
     * @return string The duration in seconds
     */
    public function __toString() 
    {
        return $this->_seconds;
    }

}
