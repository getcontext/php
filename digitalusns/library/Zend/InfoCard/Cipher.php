<?php

namespace Zend\InfoCard;


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
 * @subpackage  Cipher 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Cipher.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  InfoCardCipherException 
 */
require_once 'Zend/InfoCard/Cipher/Exception.php';

/**
 * Provides an abstraction \for encryption ciphers used in an Information Card
 * implementation
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage  Cipher 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Cipher\Symmetric\Adapter\Aes256cbc as Aes256cbc;
use Zend\InfoCard\Cipher\Symmetric\Adapter\Aes128cbc as Aes128cbc;
use Zend\InfoCard\Cipher\Pki\Adapter\Rsa as Rsa;
use Zend\InfoCard\Cipher\Exception as InfoCardCipherException;




class  Cipher 
{
    /**
     * AES 256 Encryption with CBC
     */
    const ENC_AES256CBC      = 'http://www.w3.org/2001/04/xmlenc#aes256-cbc';

    /**
     * AES 128 Encryption with CBC
     */
    const ENC_AES128CBC      = 'http://www.w3.org/2001/04/xmlenc#aes128-cbc';

    /**
     * RSA Public Key Encryption with OAEP Padding
     */
    const ENC_RSA_OAEP_MGF1P = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';

    /**
     * RSA Public Key Encryption with no padding
     */
    const ENC_RSA            = 'http://www.w3.org/2001/04/xmlenc#rsa-1_5';

    /**
     * Constructor (disabled)
     *
     * @return void
     * @codeCoverageIgnoreStart
     */
    private function __construct()
    {
    }
    // @codeCoverageIgnoreEnd
    /**
     * Returns an instance \of a cipher object supported based on the URI provided
     *
     * @throws  InfoCardCipherException 
     * @param string $uri The URI \of the encryption method wantde
     * @return mixed an Instance \of  Cipher _Symmetric_Interface or  Cipher _Pki_Interface
     *               depending on URI
     */
    static public function getInstanceByURI($uri)
    {
        switch($uri) {
            case self::ENC_AES256CBC:
                include_once 'Zend/InfoCard/Cipher/Symmetric/Adapter/Aes256cbc.php';
                return new  Aes256cbc ();

            case self::ENC_AES128CBC:
                include_once 'Zend/InfoCard/Cipher/Symmetric/Adapter/Aes128cbc.php';
                return new  Aes128cbc ();

            case self::ENC_RSA_OAEP_MGF1P:
                include_once 'Zend/InfoCard/Cipher/Pki/Adapter/Rsa.php';
                return new  Rsa ( Cipher _Pki_Adapter_Rsa::OAEP_PADDING);
                break;

            case self::ENC_RSA:
                include_once 'Zend/InfoCard/Cipher/Pki/Adapter/Rsa.php';
                return new  Rsa ( Cipher _Pki_Adapter_Rsa::NO_PADDING);
                break;

            default:
                throw new  InfoCardCipherException ("Unknown Cipher URI");
        }
    }
}
