<?php

namespace Zend\Gdata\Calendar\Extension;



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
 * \Data model class to represent an entry's sendEventNotifications
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Extension\SendEventNotifications as GdataExtensionSendEventNotifications;
use Zend\Gdata\App\InvalidArgumentException as GdataAppInvalidArgumentException;
use Zend\Gdata\Extension as Extension;
use Zend\Gdata\Calendar as Calendar;




class  SendEventNotifications  extends  Extension 
{
    protected $_rootNamespace = 'gCal';
    protected $_rootElement = 'sendEventNotifications';
    protected $_value = null;

    /**
     * Constructs a new  GdataExtensionSendEventNotifications  object.
     * @param bool $value (optional) SendEventNotifications value as URI.
     */
    public function __construct($value = null)
    {
        foreach ( Calendar ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->_value = $value;
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
        if ($this->_value !== null) {
            $element->setAttribute('value', ($this->_value ? "true" : "false"));
        }
        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'value':
            if ($attribute->nodeValue == "true") {
                $this->_value = true;
            }
            else if ($attribute->nodeValue == "false") {
                $this->_value = false;
            }
            else {
                throw new  GdataAppInvalidArgumentException ("Expected 'true' or 'false' \for gCal:selected#value.");
            }
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value \for this element's Value attribute.
     *
     * @return string The requested attribute.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the value \for this element's Value attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  GdataExtensionSendEventNotifications  The element being modified.
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     */
    public function __toString()
    {
        return $this->getValue();
    }

}

