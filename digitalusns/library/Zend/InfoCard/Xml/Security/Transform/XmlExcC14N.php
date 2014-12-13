<?php

namespace Zend\InfoCard\Xml\Security\Transform;


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
 * @subpackage \Zend\InfoCard\Xml\Security
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: XmlExcC14N.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  InfoCardXmlSecurityTransformInterface 
 */
require_once 'Zend/InfoCard/Xml/Security/Transform/Interface.php';

/**
 *  InfoCardXmlSecurityTransformException 
 */
require_once 'Zend/InfoCard/Xml/Security/Transform/Exception.php';

/**
 * A Transform to perform C14n XML Exclusive Canonicalization
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard\Xml\Security
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Xml\Security\Transform\TransformInterface as InfoCardXmlSecurityTransformInterface;
use Zend\InfoCard\Xml\Security\Transform\Exception as InfoCardXmlSecurityTransformException;




class  XmlExcC14N 
    implements  InfoCardXmlSecurityTransformInterface 
{
    /**
     * Transform the input XML based on C14n XML Exclusive Canonicalization rules
     *
     * @throws  InfoCardXmlSecurityTransformException 
     * @param string $strXMLData The input XML
     * @return string The output XML
     */
    public function transform($strXMLData)
    {
        $dom = new DOMDocument();
        $dom->loadXML($strXMLData);

        if(method_exists($dom, 'C14N')) {
            return $dom->C14N(true, false);
        }

        throw new  InfoCardXmlSecurityTransformException ("This transform requires the C14N() method to exist in the DOM extension");
    }
}
