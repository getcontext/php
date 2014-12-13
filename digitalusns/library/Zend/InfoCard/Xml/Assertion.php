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
 * @version    $Id: Assertion.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  InfoCardXmlException 
 */
require_once 'Zend/InfoCard/Xml/Exception.php';

/**
 *  Assertion _Interface
 */
require_once 'Zend/InfoCard/Xml/Assertion/Interface.php';

/**
 * Factory object to retrieve an Assertion object based on the type \of XML document provided
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\InfoCard\Xml\Exception as InfoCardXmlException;
use Zend\InfoCard\Xml\Element as Element;



final class  Assertion 
{
    /**
     * The namespace \for a SAML-formatted Assertion document
     */
    const TYPE_SAML = 'urn:oasis:names:tc:SAML:1.0:assertion';

    /**
     * Constructor (disabled)
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance \of a InfoCard Assertion object based on the XML data provided
     *
     * @throws  InfoCardXmlException 
     * @param string $xmlData The XML-Formatted Assertion
     * @return  Assertion _Interface
     * @throws  InfoCardXmlException 
     */
    static public function getInstance($xmlData)
    {

        if($xmlData instanceof  Element ) {
            $strXmlData = $xmlData->asXML();
        } else if (is_string($xmlData)) {
            $strXmlData = $xmlData;
        } else {
            throw new  InfoCardXmlException ("Invalid \Data provided to create instance");
        }

        $sxe = simplexml_load_string($strXmlData);

        $namespaces = $sxe->getDocNameSpaces();

        foreach($namespaces as $namespace) {
            switch($namespace) {
                case self::TYPE_SAML:
                    include_once 'Zend/InfoCard/Xml/Assertion/Saml.php';
                    return simplexml_load_string($strXmlData, '\Zend\InfoCard\Xml\Assertion\Saml', null);
            }
        }

        throw new  InfoCardXmlException ("Unable to determine Assertion type by Namespace");
    }
}
