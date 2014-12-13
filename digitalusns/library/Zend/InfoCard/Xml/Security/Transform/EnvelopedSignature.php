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
 * @version    $Id: EnvelopedSignature.php 9094 2008-03-30 18:36:55Z thomas $
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
 * A object implementing the EnvelopedSignature XML Transform
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard\Xml\Security
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Xml\Security\Transform\TransformInterface as InfoCardXmlSecurityTransformInterface;
use Zend\InfoCard\Xml\Security\Transform\Exception as InfoCardXmlSecurityTransformException;




class  EnvelopedSignature 
    implements  InfoCardXmlSecurityTransformInterface 
{
    /**
     * Transforms the XML Document according to the EnvelopedSignature Transform
     *
     * @throws  InfoCardXmlSecurityTransformException 
     * @param string $strXMLData The input XML data
     * @return string the transformed XML data
     */
    public function transform($strXMLData)
    {
        $sxe = simplexml_load_string($strXMLData);

        if(!$sxe->Signature) {
            throw new  InfoCardXmlSecurityTransformException ("Unable to locate Signature Block \for EnvelopedSignature Transform");
        }

        unset($sxe->Signature);

        return $sxe->asXML();
    }
}
