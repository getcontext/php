<?php

namespace Zend\Captcha;


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
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Base  */
require_once 'Zend/Captcha/Base.php';

/**  ServiceReCaptcha  */
require_once 'Zend/Service/ReCaptcha.php';

/**
 * ReCaptcha adapter
 * 
 * Allows to insert captchas driven by ReCaptcha service
 * 
 * @see http://recaptcha.net/apidocs/captcha/
 *
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


use Zend\Form\Captcha\Adapter as Adapter;
use Zend\Validate\ValidateInterface as ValidateInterface;
use Zend\Service\ReCaptcha as ServiceReCaptcha;
use Zend\Captcha\Base as Base;
use Zend\Config as Config;
use Zend\View as ZendView;




class  ReCaptcha  extends  Base  
{
    /**
     * Recaptcha public key
     *
     * @var string
     */
    protected $_pubkey;

    /**
     * Recaptcha private key
     *
     * @var string
     */
    protected $_privkey;
    
    /**@+
     * ReCaptcha Field names 
     * @var string
     */
    protected $_CHALLENGE = 'recaptcha_challenge_field';
    protected $_RESPONSE  = 'recaptcha_response_field';
    /**@-*/
     
    /**
     * Recaptcha service object
     *
     * @var Zend_Service_Recaptcha
     */
    protected $_service;

    /**
     * Parameters defined by the service
     * 
     * @var array
     */
    protected $_serviceParams = array();

    /**#@+
     * Error codes
     * @const string
     */
    const MISSING_VALUE = 'missingValue';
    const ERR_CAPTCHA   = 'errCaptcha';
    const BAD_CAPTCHA   = 'badCaptcha';
    /**#@-*/

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::MISSING_VALUE => 'Missing captcha fields',
        self::ERR_CAPTCHA   => 'Failed to validate captcha',
        self::BAD_CAPTCHA   => 'Captcha value is wrong: %value%',
    );
	
	/**
     * Retrieve ReCaptcha Private key
     *
	 * @return string
	 */
    public function getPrivkey() 
    {
		return $this->_privkey;
	}
	
	/**
     * Retrieve ReCaptcha Public key
     *
	 * @return string
	 */
    public function getPubkey() 
    {
		return $this->_pubkey;
	}
	
	/**
     * Set ReCaptcha Private key
     *
	 * @param string $_privkey
     * @return  ReCaptcha 
	 */
    public function setPrivkey($privkey) 
    {
		$this->_privkey = $privkey;
		return $this;
	}
	
	/**
     * Set ReCaptcha public key
     *
	 * @param string $_pubkey
     * @return  ReCaptcha 
	 */
    public function setPubkey($pubkey) 
    {
		$this->_pubkey = $pubkey;
		return $this;
	}
	
    /**
     * Constructor
     *
     * @param  array| Config  $options 
     * @return void
     */
	public function __construct($options = null)
	{
        parent::__construct($options);

	    $this->setService(new  ServiceReCaptcha ($this->getPubKey(), $this->getPrivKey()));
	    $this->_serviceParams = $this->getService()->getParams();

        if ($options instanceof  Config ) {
            $options = $options->toArray();
        }
        if (!empty($options)) {
            $this->setOptions($options);
        }
	}

    /**
     * Set service object
     * 
     * @param   ServiceReCaptcha  $service 
     * @return  ReCaptcha 
     */
    public function setService( ServiceReCaptcha  $service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * Retrieve ReCaptcha service object
     * 
     * @return  ServiceReCaptcha 
     */
    public function getService()
    {
        return $this->_service;
    }

    /**
     * Set option
     *
     * If option is a service parameter, proxies to the service.
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return  ReCaptcha 
     */
    public function setOption($key, $value)
    {
        $service = $this->getService();
        if (isset($this->_serviceParams[$key])) {
            $service->setParam($key, $value);
            return $this;
        }
        return parent::setOption($key, $value);
    }
	
    /**
     * Generate captcha
     *
     * @see  Adapter ::generate()
     * @return string
     */
    public function generate()
    {
        return "";
    }

    /**
     * Validate captcha
     *
     * @see     ValidateInterface ::isValid()
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (empty($context[$this->_CHALLENGE]) || empty($context[$this->_RESPONSE])) {
            $this->_error(self::MISSING_VALUE);
            return false;
        }

        $service = $this->getService();
        
        $res = $service->verify($context[$this->_CHALLENGE], $context[$this->_RESPONSE]); 
        
        if (!$res) {
            $this->_error(self::ERR_CAPTCHA);
            return false;
        }
        
        if (!$res->isValid()) {
            $this->_error(self::BAD_CAPTCHA, $res->getErrorCode());
            $service->setParam('error', $res->getErrorCode());
            return false;
        }

        return true;
    }
    
    /**
     * Render captcha
     * 
     * @param   ZendView  $view 
     * @param  mixed $element 
     * @return string
     */
    public function render( ZendView  $view, $element = null)
    {
        return $this->getService()->getHTML();
    }
}
