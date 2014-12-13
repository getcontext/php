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
 * @version    $Id: EncryptedKey.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  Element 
 */
require_once 'Zend/InfoCard/Xml/Element.php';

/**
 *  EncryptedKey 
 */
require_once 'Zend/InfoCard/Xml/EncryptedKey.php';

/**
 *  InfoCardXmlKeyInfoInterface 
 */
require_once 'Zend/InfoCard/Xml/KeyInfo/Interface.php';

/**
 * An object representing an \Xml EncryptedKEy block
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Xml\KeyInfo\KeyInfoInterface as InfoCardXmlKeyInfoInterface;
use Zend\InfoCard\Xml\Exception as InfoCardXmlException;
use Zend\InfoCard\Xml\Element as Element;
use Zend\InfoCard\Xml\KeyInfo as KeyInfo;




class  EncryptedKey 
    extends  Element 
    implements  InfoCardXmlKeyInfoInterface 
{
    /**
     * Return an instance \of the object based on input XML \Data
     *
     * @throws  InfoCardXmlException 
     * @param string $xmlData The EncryptedKey XML Block
     * @return  EncryptedKey 
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

        if($sxe->getName() != "EncryptedKey") {
            throw new  InfoCardXmlException ("Invalid XML Block provided \for EncryptedKey");
        }

        return simplexml_load_string($strXmlData, "\Zend\InfoCard\Xml\EncryptedKey");
    }

    /**
     * Returns the Encyption Method Algorithm URI \of the block
     *
     * @throws  InfoCardXmlException 
     * @return string the Encryption method algorithm URI
     */
    public function getEncryptionMethod()
    {

        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
        list($encryption_method) = $this->xpath("//e:EncryptionMethod");

        if(!($encryption_method instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to find the e:EncryptionMethod KeyInfo encryption block");
        }

        $dom = self::convertToDOM($encryption_method);

        if(!$dom->hasAttribute('Algorithm')) {
            throw new  InfoCardXmlException ("Unable to determine the encryption algorithm in the Symmetric enc:EncryptionMethod XML block");
        }

        return $dom->getAttribute('Algorithm');

    }

    /**
     * Returns the Digest Method Algorithm URI used
     *
     * @throws  InfoCardXmlException 
     * @return string the Digest Method Algorithm URI
     */
    public function getDigestMethod()
    {
        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
        list($encryption_method) = $this->xpath("//e:EncryptionMethod");

        if(!($encryption_method instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to find the e:EncryptionMethod KeyInfo encryption block");
        }

        if(!($encryption_method->DigestMethod instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to find the DigestMethod block");
        }

        $dom = self::convertToDOM($encryption_method->DigestMethod);

        if(!$dom->hasAttribute('Algorithm')) {
            throw new  InfoCardXmlException ("Unable to determine the digest algorithm \for the symmetric Keyinfo");
        }

        return $dom->getAttribute('Algorithm');

    }

    /**
     * Returns the KeyInfo block object
     *
     * @throws  InfoCardXmlException 
     * @return  KeyInfo _Abstract
     */
    public function getKeyInfo()
    {

        if(isset($this->KeyInfo)) {
            return  KeyInfo ::getInstance($this->KeyInfo);
        }

        throw new  InfoCardXmlException ("Unable to locate a KeyInfo block");
    }

    /**
     * Return the encrypted value \of the block in base64 format
     *
     * @throws  InfoCardXmlException 
     * @return string The Value \of the CipherValue block in base64 format
     */
    public function getCipherValue()
    {

        $this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');

        list($cipherdata) = $this->xpath("//e:CipherData");

        if(!($cipherdata instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to find the e:CipherData block");
        }

        $cipherdata->registerXPathNameSpace('enc', 'http://www.w3.org/2001/04/xmlenc#');
        list($ciphervalue) = $cipherdata->xpath("//enc:CipherValue");

        if(!($ciphervalue instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to fidn the enc:CipherValue block");
        }

        return (string)$ciphervalue;
    }
}
