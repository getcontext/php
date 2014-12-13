<?php

namespace Zend\Auth\Adapter;


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
 * @package    \Zend\Auth
 * @subpackage \Zend\Auth_Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: InfoCard.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 * @see  AuthAdapterInterface 
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see  Result 
 */
require_once 'Zend/Auth/Result.php';

/**
 * @see  ZendInfoCard 
 */
require_once 'Zend/InfoCard.php';

/**
 * A \Zend\Auth Authentication Adapter allowing the use \of Information Cards as an
 * authentication mechanism
 *
 * @category   Zend
 * @package    \Zend\Auth
 * @subpackage \Zend\Auth_Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Cipher\Symmetric\SymmetricInterface as InfoCardCipherSymmetricInterface;
use Zend\InfoCard\Cipher\PKI\PKIInterface as InfoCardCipherPKIInterface;
use Zend\InfoCard\Adapter\AdapterInterface as InfoCardAdapterInterface;
use Zend\Auth\Adapter\AdapterInterface as AuthAdapterInterface;
use Zend\InfoCard\Cipher as Cipher;
use Zend\infoCard\Claims as Claims;
use Zend\Auth\Result as Result;
use Zend\InfoCard as ZendInfoCard;




class  InfoCard  implements  AuthAdapterInterface 
{
    /**
     * The XML Token being authenticated
     *
     * @var string
     */
    protected $_xmlToken;

    /**
     * The instance \of  ZendInfoCard 
     *
     * @var  ZendInfoCard 
     */
    protected $_infoCard;

    /**
     * Constructor
     *
     * @param  string $strXmlDocument The XML Token provided by the client
     * @return void
     */
    public function __construct($strXmlDocument)
    {
        $this->_xmlToken = $strXmlDocument;
        $this->_infoCard = new  ZendInfoCard ();
    }

    /**
     * Sets the InfoCard component Adapter to use
     *
     * @param   InfoCardAdapterInterface  $a
     * @return  InfoCard  Provides a fluent interface
     */
    public function setAdapter( InfoCardAdapterInterface  $a)
    {
        $this->_infoCard->setAdapter($a);
        return $this;
    }

    /**
     * Retrieves the InfoCard component adapter being used
     *
     * @return  InfoCardAdapterInterface 
     */
    public function getAdapter()
    {
        return $this->_infoCard->getAdapter();
    }

    /**
     * Retrieves the InfoCard public key cipher object being used
     *
     * @return  InfoCardCipherPKIInterface 
     */
    public function getPKCipherObject()
    {
        return $this->_infoCard->getPKCipherObject();
    }

    /**
     * Sets the InfoCard public key cipher object to use
     *
     * @param   InfoCardCipherPKIInterface  $cipherObj
     * @return  InfoCard  Provides a fluent interface
     */
    public function setPKICipherObject( InfoCardCipherPKIInterface  $cipherObj)
    {
        $this->_infoCard->setPKICipherObject($cipherObj);
        return $this;
    }

    /**
     * Retrieves the Symmetric cipher object being used
     *
     * @return  InfoCardCipherSymmetricInterface 
     */
    public function getSymCipherObject()
    {
        return $this->_infoCard->getSymCipherObject();
    }

    /**
     * Sets the InfoCard symmetric cipher object to use
     *
     * @param   InfoCardCipherSymmetricInterface  $cipherObj
     * @return  InfoCard  Provides a fluent interface
     */
    public function setSymCipherObject( InfoCardCipherSymmetricInterface  $cipherObj)
    {
        $this->_infoCard->setSymCipherObject($cipherObj);
        return $this;
    }

    /**
     * Remove a Certificate Pair by Key ID from the search list
     *
     * @param  string $key_id The Certificate Key ID returned from adding the certificate pair
     * @throws  ZendInfoCard _Exception
     * @return  InfoCard  Provides a fluent interface
     */
    public function removeCertificatePair($key_id)
    {
        $this->_infoCard->removeCertificatePair($key_id);
        return $this;
    }

    /**
     * Add a Certificate Pair to the list \of certificates searched by the component
     *
     * @param  string $private_key_file    The path to the private key file \for the pair
     * @param  string $public_key_file     The path to the certificate / public key \for the pair
     * @param  string $type                (optional) The URI \for the type \of key pair this is (default RSA with OAEP padding)
     * @param  string $password            (optional) The password \for the private key file if necessary
     * @throws  ZendInfoCard _Exception
     * @return string A key ID representing this key pair in the component
     */
    public function addCertificatePair($private_key_file, $public_key_file, $type =  Cipher ::ENC_RSA_OAEP_MGF1P, $password = null)
    {
        return $this->_infoCard->addCertificatePair($private_key_file, $public_key_file, $type, $password);
    }

    /**
     * Return a Certificate Pair from a key ID
     *
     * @param  string $key_id The Key ID \of the certificate pair in the component
     * @throws  ZendInfoCard _Exception
     * @return array An array containing the path to the private/public key files,
     *               the type URI and the password if provided
     */
    public function getCertificatePair($key_id)
    {
        return $this->_infoCard->getCertificatePair($key_id);
    }

    /**
     * Set the XML Token to be processed
     *
     * @param  string $strXmlToken The XML token to process
     * @return  InfoCard  Provides a fluent interface
     */
    public function setXmlToken($strXmlToken)
    {
        $this->_xmlToken = $strXmlToken;
        return $this;
    }

    /**
     * Get the XML Token being processed
     *
     * @return string The XML token to be processed
     */
    public function getXmlToken()
    {
        return $this->_xmlToken;
    }

    /**
     * Authenticates the XML token
     *
     * @return  Result  The result \of the authentication
     */
    public function authenticate()
    {
        try {
            $claims = $this->_infoCard->process($this->getXmlToken());
        } catch(\Exception $e) {
            return new  Result (\Zend\Auth\Result::FAILURE , null, array('\Exception Thrown',
                                                                                $e->getMessage(),
                                                                                $e->getTraceAsString(),
                                                                                serialize($e)));
        }

        if(!$claims->isValid()) {
            switch($claims->getCode()) {
                case  Claims ::RESULT_PROCESSING_FAILURE:
                    return new  Result (
                         Result ::FAILURE,
                        $claims,
                        array(
                            'Processing Failure',
                            $claims->getErrorMsg()
                        )
                    );
                    break;
                case  ZendInfoCard _Claims::RESULT_VALIDATION_FAILURE:
                    return new  Result (
                         Result ::FAILURE_CREDENTIAL_INVALID,
                        $claims,
                        array(
                            'Validation Failure',
                            $claims->getErrorMsg()
                        )
                    );
                    break;
                default:
                    return new  Result (
                         Result ::FAILURE,
                        $claims,
                        array(
                            'Unknown Failure',
                            $claims->getErrorMsg()
                        )
                    );
                    break;
            }
        }

        return new  Result (
             Result ::SUCCESS,
            $claims
        );
    }
}
