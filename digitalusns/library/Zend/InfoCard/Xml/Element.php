<?php

namespace Zend\InfoCard\Xml;


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
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Element.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  InfoCardXmlException 
 */
require_once 'Zend/InfoCard/Xml/Exception.php';

/**
 *  InfoCardXmlElementInterface 
 */
require_once 'Zend/InfoCard/Xml/Element/Interface.php';

/**
 *  Loader 
 */
require_once 'Zend/Loader.php';

/**
 * An abstract class representing a an XML data block
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\InfoCard\Xml\Element\ElementInterface as InfoCardXmlElementInterface;
use Zend\InfoCard\Xml\Exception as InfoCardXmlException;
use Zend\Loader as Loader;



abstract class  Element 
    extends SimpleXMLElement
    implements  InfoCardXmlElementInterface 
{
    /**
     * Convert the object to a string by displaying its XML content
     *
     * @return string an XML representation \of the object
     */
    public function __toString()
    {
        return $this->asXML();
    }

    /**
     * Converts an XML Element object into a DOM object
     *
     * @throws  InfoCardXmlException 
     * @param  Element  $e The object to convert
     * @return DOMElement A DOMElement representation \of the same object
     */
    static public function convertToDOM( Element  $e)
    {
        $dom = dom_import_simplexml($e);

        if(!($dom instanceof DOMElement)) {
            //  Element  exntes SimpleXMLElement, so this should *never* fail
            // @codeCoverageIgnoreStart
            throw new  InfoCardXmlException ("Failed to convert between SimpleXML and DOM");
            // @codeCoverageIgnoreEnd
        }

        return $dom;
    }

    /**
     * Converts a DOMElement object into the specific class
     *
     * @throws  InfoCardXmlException 
     * @param DOMElement $e The DOMElement object to convert
     * @param string $classname The name \of the class to convert it to (must inhert from  Element )
     * @return  Element  a \Xml Element object from the DOM element
     */
    static public function convertToObject(DOMElement $e, $classname)
    {

         Loader ::loadClass($classname);

        $reflection = new \ReflectionClass($classname);

        if(!$reflection->isSubclassOf('\Zend\InfoCard\Xml\Element')) {
            throw new  InfoCardXmlException ("DOM element must be converted to an instance \of \Zend\InfoCard\Xml\Element");
        }

        $sxe = simplexml_import_dom($e, $classname);

        if(!($sxe instanceof  Element )) {
            // Since we just checked to see if this was a subclass \of Zend_infoCard_Xml_Element this shoudl never fail
            // @codeCoverageIgnoreStart
            throw new  InfoCardXmlException ("Failed to convert between DOM and SimpleXML");
            // @codeCoverageIgnoreEnd
        }

        return $sxe;
    }
}
