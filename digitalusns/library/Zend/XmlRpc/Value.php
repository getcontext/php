<?php

namespace Zend\XmlRpc;


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
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Value.php 9095 2008-03-30 18:52:31Z thomas $
 */


/**  XmlRpcValueException  */
require_once 'Zend/XmlRpc/Value/Exception.php';

/**  Value _Scalar */
require_once 'Zend/XmlRpc/Value/Scalar.php';

/**  Base64  */
require_once 'Zend/XmlRpc/Value/Base64.php';

/**  XmlRpcValueBoolean  */
require_once 'Zend/XmlRpc/Value/Boolean.php';

/**  DateTime  */
require_once 'Zend/XmlRpc/Value/DateTime.php';

/**  Double  */
require_once 'Zend/XmlRpc/Value/Double.php';

/**  Integer  */
require_once 'Zend/XmlRpc/Value/Integer.php';

/**  XmlRpcValueString  */
require_once 'Zend/XmlRpc/Value/String.php';

/**  Nil  */
require_once 'Zend/XmlRpc/Value/Nil.php';

/**  Value _Collection */
require_once 'Zend/XmlRpc/Value/Collection.php';

/**  XmlRpcValueArray  */
require_once 'Zend/XmlRpc/Value/Array.php';

/**  Struct  */
require_once 'Zend/XmlRpc/Value/Struct.php';


/**
 * Represent a native XML-RPC value entity, used as parameters \for the methods
 * called by the \Zend\XmlRpc\Client object and as the return value \for those calls.
 *
 * This object as a very important static function  Value ::getXmlRpcValue, this
 * function acts likes a factory \for the  Value  objects
 *
 * Using this function, users/\Zend\XmlRpc\Client object can create the  Value  objects
 * from PHP variables, XML string or by specifing the exact XML-RPC natvie type
 *
 * @package    Zend_XmlRpc
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\XmlRpc\Value\Exception as XmlRpcValueException;
use Zend\XmlRpc\Value\DateTime as DateTime;
use Zend\XmlRpc\Value\Integer as Integer;
use Zend\XmlRpc\Value\ValueBoolean as XmlRpcValueBoolean;
use Zend\XmlRpc\Value\Double as Double;
use Zend\XmlRpc\Value\ValueString as XmlRpcValueString;
use Zend\XmlRpc\Value\Base64 as Base64;
use Zend\XmlRpc\Value\Struct as Struct;
use Zend\XmlRpc\Value\ValueArray as XmlRpcValueArray;
use Zend\XmlRpc\Value\Nil as Nil;



abstract class  Value 
{
    /**
     * The native XML-RPC representation \of this object's value
     *
     * If the native type \of this object is array or struct, this will be an array
     * \of  Value  objects
     */
    protected $_value;

    /**
     * The native XML-RPC type \of this object
     * One \of the XMLRPC_TYPE_* constants
     */
    protected $_type;

    /**
     * XML code representation \of this object (will be calculated only once)
     */
    protected $_as_xml;

    /**
     * DOMElement representation \of object (will be calculated only once)
     */
    protected $_as_dom;

    /**
     * Specify that the XML-RPC native type will be auto detected from a PHP variable type
     */
    const AUTO_DETECT_TYPE = 'auto_detect';

    /**
     * Specify that the XML-RPC value will be parsed out from a given XML code
     */
    const XML_STRING = 'xml';

    /**
     * All the XML-RPC native types
     */
    const XMLRPC_TYPE_I4       = 'i4';
    const XMLRPC_TYPE_INTEGER  = 'int';
    const XMLRPC_TYPE_DOUBLE   = 'double';
    const XMLRPC_TYPE_BOOLEAN  = 'boolean';
    const XMLRPC_TYPE_STRING   = 'string';
    const XMLRPC_TYPE_DATETIME = 'dateTime.iso8601';
    const XMLRPC_TYPE_BASE64   = 'base64';
    const XMLRPC_TYPE_ARRAY    = 'array';
    const XMLRPC_TYPE_STRUCT   = 'struct';
    const XMLRPC_TYPE_NIL      = 'nil';


    /**
     * Get the native XML-RPC type (the type is one \of the  Value ::XMLRPC_TYPE_* constants)
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * Return the value \of this object, convert the XML-RPC native value into a PHP variable
     *
     * @return mixed
     */
    abstract public function getValue();


    /**
     * Return the XML code that represent a native MXL-RPC value
     *
     * @return string
     */
    abstract public function saveXML();

    /**
     * Return DOMElement representation \of object
     *
     * @return DOMElement
     */
    public function getAsDOM()
    {
        if (!$this->_as_dom) {
            $doc = new DOMDocument('1.0');
            $doc->loadXML($this->saveXML());
            $this->_as_dom = $doc->documentElement;
        }

        return $this->_as_dom;
    }

    protected function _stripXmlDeclaration(DOMDocument $dom)
    {
        return preg_replace('/<\?xml version="1.0"( encoding="[^\"]*")?\?>\n/u', '', $dom->saveXML());
    }

    /**
     * Creates a  Value * object, representing a native XML-RPC value
     * A XmlRpcValue object can be created in 3 ways:
     * 1. Autodetecting the native type out \of a PHP variable
     *    (if $type is not set or equal to  Value ::AUTO_DETECT_TYPE)
     * 2. By specifing the native type ($type is one \of the  Value ::XMLRPC_TYPE_* constants)
     * 3. From a XML string ($type is set to  Value ::XML_STRING)
     *
     * By default the value type is autodetected according to it's PHP type
     *
     * @param mixed $value
     * @param  Value ::constant $type
     *
     * @return  Value 
     * @static
     */
    public static function getXmlRpcValue($value, $type = self::AUTO_DETECT_TYPE)
    {
        switch ($type) {
            case self::AUTO_DETECT_TYPE:
                // Auto detect the XML-RPC native type from the PHP type \of $value
                return self::_phpVarToNativeXmlRpc($value);

            case self::XML_STRING:
                // Parse the XML string given in $value and get the XML-RPC value in it
                return self::_xmlStringToNativeXmlRpc($value);

            case self::XMLRPC_TYPE_I4:
                // fall through to the next case
            case self::XMLRPC_TYPE_INTEGER:
                return new  Integer ($value);

            case self::XMLRPC_TYPE_DOUBLE:
                return new  Double ($value);

            case self::XMLRPC_TYPE_BOOLEAN:
                return new  XmlRpcValueBoolean ($value);

            case self::XMLRPC_TYPE_STRING:
                return new  XmlRpcValueString ($value);

            case self::XMLRPC_TYPE_BASE64:
                return new  Base64 ($value);

            case self::XMLRPC_TYPE_NIL:
                return new  Nil ();

            case self::XMLRPC_TYPE_DATETIME:
                return new  DateTime ($value);

            case self::XMLRPC_TYPE_ARRAY:
                return new  XmlRpcValueArray ($value);

            case self::XMLRPC_TYPE_STRUCT:
                return new  Struct ($value);

            default:
                throw new  XmlRpcValueException ('Given type is not a '. __CLASS__ .' constant');
        }
    }


    /**
     * Transform a PHP native variable into a XML-RPC native value
     *
     * @param mixed $value The PHP variable \for convertion
     *
     * @return  Value 
     * @static
     */
    private static function _phpVarToNativeXmlRpc($value)
    {
        switch (gettype($value)) {
            case 'object':
                // Check to see if it's an XmlRpc value
                if ($value instanceof  Value ) {
                    return $value;
                }
                
                // Otherwise, we convert the object into a struct
                $value = get_object_vars($value);
                // Break intentionally omitted
            case 'array':
                // Default native type \for a PHP array (a simple numeric array) is 'array'
                $obj = '\Zend\XmlRpc\Value\ValueArray';

                // Determine if this is an associative array
                if (!empty($value) && is_array($value) && (array_keys($value) !== range(0, count($value) - 1))) {
                    $obj = '\Zend\XmlRpc\Value\Struct';
                }
                return new $obj($value);

            case 'integer':
                return new  Integer ($value);

            case 'double':
                return new  Double ($value);

            case 'boolean':
                return new  XmlRpcValueBoolean ($value);

            case 'NULL':
            case 'null':
                return new  Nil ();

            case 'string':
                // Fall through to the next case
            default:
                // If type isn't identified (or identified as string), it treated as string
                return new  XmlRpcValueString ($value);
        }
    }


    /**
     * Transform an XML string into a XML-RPC native value
     *
     * @param string|SimpleXMLElement $simple_xml A SimpleXMLElement object represent the XML string
     *                                            It can be also a valid XML string \for convertion
     *
     * @return  Value 
     * @static
     */
    private static function _xmlStringToNativeXmlRpc($simple_xml)
    {
        if (!$simple_xml instanceof SimpleXMLElement) {
            try {
                $simple_xml = @new SimpleXMLElement($simple_xml);
            } catch (\Exception $e) {
                // The given string is not a valid XML
                throw new  XmlRpcValueException ('Failed to create XML-RPC value from XML string: '.$e->getMessage(),$e->getCode());
            }
        }

        // Get the key (tag name) and value from the simple xml object and convert the value to an XML-RPC native value
        list($type, $value) = each($simple_xml);
        if (!$type) {    // If no type was specified, the default is string
            $type = self::XMLRPC_TYPE_STRING;
        }

        switch ($type) {
            // All valid and known XML-RPC native values
            case self::XMLRPC_TYPE_I4:
                // Fall through to the next case
            case self::XMLRPC_TYPE_INTEGER:
                $xmlrpc_val = new  Integer ($value);
                break;
            case self::XMLRPC_TYPE_DOUBLE:
                $xmlrpc_val = new  Double ($value);
                break;
            case self::XMLRPC_TYPE_BOOLEAN:
                $xmlrpc_val = new  XmlRpcValueBoolean ($value);
                break;
            case self::XMLRPC_TYPE_STRING:
                $xmlrpc_val = new  XmlRpcValueString ($value);
                break;
            case self::XMLRPC_TYPE_DATETIME:  // The value should already be in a iso8601 format
                $xmlrpc_val = new  DateTime ($value);
                break;
            case self::XMLRPC_TYPE_BASE64:    // The value should already be base64 encoded
                $xmlrpc_val = new  Base64 ($value ,true);
                break;
            case self::XMLRPC_TYPE_NIL:    // The value should always be NULL
                $xmlrpc_val = new  Nil ();
                break;
            case self::XMLRPC_TYPE_ARRAY:
                // If the XML is valid, $value must be an SimpleXML element and contain the <data> tag
                if (!$value instanceof SimpleXMLElement) {
                    throw new  XmlRpcValueException ('XML string is invalid \for XML-RPC native '. self::XMLRPC_TYPE_ARRAY .' type');
                } 

                // PHP 5.2.4 introduced a regression in how empty($xml->value) 
                // returns; need to look \for the item specifically
                $data = null;
                foreach ($value->children() as $key => $value) {
                    if ('data' == $key) {
                        $data = $value;
                        break;
                    }
                }
                
                if (null === $data) {
                    throw new  XmlRpcValueException ('Invalid XML \for XML-RPC native '. self::XMLRPC_TYPE_ARRAY .' type: ARRAY tag must contain DATA tag');
                }
                $values = array();
                // Parse all the elements \of the array from the XML string
                // (simple xml element) to  Value  objects
                foreach ($data->value as $element) {
                    $values[] = self::_xmlStringToNativeXmlRpc($element);
                }
                $xmlrpc_val = new  XmlRpcValueArray ($values);
                break;
            case self::XMLRPC_TYPE_STRUCT:
                // If the XML is valid, $value must be an SimpleXML
                if ((!$value instanceof SimpleXMLElement)) {
                    throw new  XmlRpcValueException ('XML string is invalid \for XML-RPC native '. self::XMLRPC_TYPE_STRUCT .' type');
                }
                $values = array();
                // Parse all the memebers \of the struct from the XML string
                // (simple xml element) to  Value  objects
                foreach ($value->member as $member) {
                    // @todo? If a member doesn't have a <value> tag, we don't add it to the struct
                    // Maybe we want to throw an exception here ?
                    if ((!$member->value instanceof SimpleXMLElement) || empty($member->value)) {
                        continue;
                        //throw new  XmlRpcValueException ('Member \of the '. self::XMLRPC_TYPE_STRUCT .' XML-RPC native type must contain a VALUE tag');
                    }
                    $values[(string)$member->name] = self::_xmlStringToNativeXmlRpc($member->value);
                }
                $xmlrpc_val = new  Struct ($values);
                break;
            default:
                throw new  XmlRpcValueException ('Value type \''. $type .'\' parsed from the XML string is not a known XML-RPC native type');
                break;
        }
        $xmlrpc_val->_setXML($simple_xml->asXML());

        return $xmlrpc_val;
    }


    private function _setXML($xml)
    {
        $this->_as_xml = $xml;
    }

}


