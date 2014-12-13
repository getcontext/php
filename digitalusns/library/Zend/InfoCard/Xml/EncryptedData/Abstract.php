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
 * @version    $Id: Abstract.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  Element 
 */
require_once 'Zend/InfoCard/Xml/Element.php';

/**
 *  KeyInfo 
 */
require_once 'Zend/InfoCard/Xml/KeyInfo.php';

/**
 * An abstract class representing a generic EncryptedData XML block. This class is extended
 * into a specific type \of EncryptedData XML block (i.e. XmlEnc) as necessary
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\InfoCard\Xml\Exception as InfoCardXmlException;
use Zend\InfoCard\Xml\Element as Element;
use Zend\InfoCard\Xml\KeyInfo as KeyInfo;



abstract class  EncryptedDataAbstract  extends  Element 
{

    /**
     * Returns the KeyInfo Block
     *
     * @return  KeyInfo _Abstract
     */
    public function getKeyInfo()
    {
        return  KeyInfo ::getInstance($this->KeyInfo[0]);
    }

    /**
     * Return the Encryption method used to encrypt the assertion document
     * (the symmetric cipher)
     *
     * @throws  InfoCardXmlException 
     * @return string The URI \of the Symmetric Encryption Method used
     */
    public function getEncryptionMethod()
    {

        /**
         * @todo This is pretty hacky unless we can always be confident that the first
         * EncryptionMethod block is the correct one (the AES or compariable symetric algorithm)..
         * the second is the PK method if provided.
         */
        list($encryption_method) = $this->xpath("//enc:EncryptionMethod");

        if(!($encryption_method instanceof  Element )) {
            throw new  InfoCardXmlException ("Unable to find the enc:EncryptionMethod symmetric encryption block");
        }

        $dom = self::convertToDOM($encryption_method);

        if(!$dom->hasAttribute('Algorithm')) {
            throw new  InfoCardXmlException ("Unable to determine the encryption algorithm in the Symmetric enc:EncryptionMethod XML block");
        }

        return $dom->getAttribute('Algorithm');
    }

    /**
     * Returns the value \of the encrypted block
     *
     * @return string the value \of the encrypted CipherValue block
     */
    abstract function getCipherValue();
}
