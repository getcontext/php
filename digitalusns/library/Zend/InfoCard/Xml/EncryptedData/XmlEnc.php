<?php

namespace Zend\InfoCard\Xml\EncryptedData;


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
 * @version    $Id: XmlEnc.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 * \Zend\InfoCard\Xml\EncryptedData/Abstract.php
 */
require_once 'Zend/InfoCard/Xml/EncryptedData/Abstract.php';

/**
 * An XmlEnc formatted EncryptedData XML block
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Xml\EncryptedData\EncryptedDataAbstract as InfoCardXmlEncryptedDataAbstract;
use Zend\InfoCard\Xml\Exception as InfoCardXmlException;
use Zend\InfoCard\Xml\Element as Element;




class  XmlEnc  extends  InfoCardXmlEncryptedDataAbstract 
{

    /**
     * Returns the Encrypted CipherValue block from the EncryptedData XML document
     *
     * @throws  InfoCardXmlException 
     * @return string The value \of the CipherValue block base64 encoded
     */
    public function getCipherValue()
    {
        $this->registerXPathNamespace('enc', 'http://www.w3.org/2001/04/xmlenc#');

        list(,$cipherdata) = $this->xpath("//enc:CipherData");

        if(!($cipherdata instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to find the enc:CipherData block");
        }

        list(,$ciphervalue) = $cipherdata->xpath("//enc:CipherValue");

        if(!($ciphervalue instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to fidn the enc:CipherValue block");
        }

        return (string)$ciphervalue;
    }
}
