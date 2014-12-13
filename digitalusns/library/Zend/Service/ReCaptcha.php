<?php

namespace Zend\Service;


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
 * @package    Zend_Service
 * @subpackage ReCaptcha
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** @see  ServiceAbstract  */
require_once 'Zend/Service/Abstract.php';

/** @see  Json  */
require_once 'Zend/Json.php';

/** @see  Response  */
require_once 'Zend/Service/ReCaptcha/Response.php';

/**
 *  ReCaptcha 
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage ReCaptcha
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


use Zend\Service\ReCaptcha\Exception as ServiceReCaptchaException;
use Zend\Service\ReCaptcha\Response as Response;
use Zend\Service\ServiceAbstract as ServiceAbstract;
use Zend\Http\Client as Client;
use Zend\Config as Config;
use Zend\Json as Json;




class  ReCaptcha  extends  ServiceAbstract 
{
    /**
     * URI to the regular API
     *
     * @var string
     */
    const API_SERVER = 'http://api.recaptcha.net';

    /**
     * URI to the secure API
     *
     * @var string
     */
    const API_SECURE_SERVER = 'https://api-secure.recaptcha.net';

    /**
     * URI to the verify server
     *
     * @var string
     */
    const VERIFY_SERVER = 'http://api-verify.recaptcha.net/verify';

    /**
     * Public key used when displaying the captcha
     *
     * @var string
     */
    protected $_publicKey = null;

    /**
     * Private key used when verifying user input
     *
     * @var string
     */
    protected $_privateKey = null;

    /**
     * Ip address used when verifying user input
     *
     * @var string
     */
    protected $_ip = null;

    /**
     * Parameters \for the object
     *
     * @var array
     */
    protected $_params = array(
        'ssl' => false, /* Use SSL or not when generating the recaptcha */
        'error' => null, /* The error message to display in the recaptcha */
        'xhtml' => false /* Enable XHTML output (this will not be XHTML Strict
                            compliant since the IFRAME is necessary when
                            Javascript is disabled) */
    );

    /**
     * Options \for tailoring reCaptcha
     *
     * See the different options on http://recaptcha.net/apidocs/captcha/client.html
     *
     * @var array
     */
    protected $_options = array(
        'theme' => 'red',
        'lang' => 'en',
    );

    /**
     * Response from the verify server
     *
     * @var  Response 
     */
    protected $_response = null;

    /**
     * Class constructor
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param array $params
     * @param array $options
     * @param string $ip
     * @param array| Config  $params
     */
    public function __construct($publicKey = null, $privateKey = null,
                                $params = null, $options = null, $ip = null)
    {
        if ($publicKey !== null) {
            $this->setPublicKey($publicKey);
        }

        if ($privateKey !== null) {
            $this->setPrivateKey($privateKey);
        }

        if ($ip !== null) {
            $this->setIp($ip);
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->setIp($_SERVER['REMOTE_ADDR']);
        }

        if ($params !== null) {
            $this->setParams($params);
        }

        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Serialize as string
     *
     * When the instance is used as a string it will display the recaptcha.
     * Since we can't throw exceptions within this method we will trigger
     * a user warning instead.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $return = $this->getHtml();
        } catch (\Exception $e) {
            $return = '';
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $return;
    }

    /**
     * Set the ip property
     *
     * @param string $ip
     * @return  ReCaptcha 
     */
    public function setIp($ip)
    {
        $this->_ip = $ip;

        return $this;
    }

    /**
     * Get the ip property
     *
     * @return string
     */
    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * Set a single parameter
     *
     * @param string $key
     * @param string $value
     * @return  ReCaptcha 
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;

        return $this;
    }

    /**
     * Set parameters
     *
     * @param array| Config  $params
     * @return  ReCaptcha 
     * @throws  ServiceReCaptchaException 
     */
    public function setParams($params)
    {
        if ($params instanceof  Config ) {
            $params = $params->toArray();
        }

        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $this->setParam($k, $v);
            }
        } else {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new  ServiceReCaptchaException (
                'Expected array or  Config  object'
            );
        }

        return $this;
    }

    /**
     * Get the parameter array
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Get a single parameter
     *
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->_params[$key];
    }

    /**
     * Set a single option
     *
     * @param string $key
     * @param string $value
     * @return  ReCaptcha 
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;

        return $this;
    }

    /**
     * Set options
     *
     * @param array| Config  $options
     * @return  ReCaptcha 
     * @throws  ServiceReCaptchaException 
     */
    public function setOptions($options)
    {
        if ($options instanceof  Config ) {
            $options = $options->toArray();
        }

        if (is_array($options)) {
            foreach ($options as $k => $v) {
                $this->setOption($k, $v);
            }
        } else {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new  ServiceReCaptchaException (
                'Expected array or  Config  object'
            );
        }

        return $this;
    }

    /**
     * Get the options array
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get a single option
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->_options[$key];
    }

    /**
     * Get the public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    /**
     * Set the public key
     *
     * @param string $publicKey
     * @return  ReCaptcha 
     */
    public function setPublicKey($publicKey)
    {
        $this->_publicKey = $publicKey;

        return $this;
    }

    /**
     * Get the private key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    /**
     * Set the private key
     *
     * @param string $privateKey
     * @return  ReCaptcha 
     */
    public function setPrivateKey($privateKey)
    {
        $this->_privateKey = $privateKey;

        return $this;
    }

    /**
     * Get the HTML code \for the captcha
     *
     * This method uses the public key to fetch a recaptcha form.
     *
     * @return string
     * @throws  ServiceReCaptchaException 
     */
    public function getHtml()
    {
        if ($this->_publicKey === null) {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new  ServiceReCaptchaException ('Missing public key');
        }

        $host = self::API_SERVER;

        if ($this->_params['ssl'] === true) {
            $host = self::API_SECURE_SERVER;
        }

        $htmlBreak = '<br>';
        $htmlInputClosing = '>';

        if ($this->_params['xhtml'] === true) {
            $htmlBreak = '<br />';
            $htmlInputClosing = '/>';
        }

        $errorPart = '';

        if (!empty($this->_params['error'])) {
            $errorPart = '&error=' . urlencode($this->_params['error']);
        }

        $reCaptchaOptions = '';

        if (!empty($this->_options)) {
            $encoded =  Json ::encode($this->_options);
            $reCaptchaOptions = <<<SCRIPT
<script type="text/javascript">
    var RecaptchaOptions = {$encoded};
</script>
SCRIPT;
        }

        $return = $reCaptchaOptions;
        $return .= <<<HTML
<script type="text/javascript"
   src="{$host}/challenge?k={$this->_publicKey}{$errorPart}">
</script>
HTML;
        $return .= <<<HTML
<noscript>
   <iframe src="{$host}/noscript?k={$this->_publicKey}{$errorPart}"
       height="300" width="500" frameborder="0"></iframe>{$htmlBreak}
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field"
       value="manual_challenge"{$htmlInputClosing}
</noscript>
HTML;

        return $return;
    }

    /**
     * Post a solution to the verify server
     *
     * @param string $challengeField
     * @param string $responseField
     * @return \Zend\Http\Response
     * @throws  ServiceReCaptchaException 
     */
    protected function _post($challengeField, $responseField)
    {
        if ($this->_privateKey === null) {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new  ServiceReCaptchaException ('Missing private key');
        }

        if ($this->_ip === null) {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new  ServiceReCaptchaException ('Missing ip address');
        }

        if (empty($challengeField)) {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';
            throw new  ServiceReCaptchaException ('Missing challenge field');
        }

        if (empty($responseField)) {
            /** @see  ServiceReCaptchaException  */
            require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new  ServiceReCaptchaException ('Missing response field');
        }

        /* Fetch an instance \of the http client */
        $httpClient = self::getHttpClient();

        $postParams = array('privatekey' => $this->_privateKey,
                            'remoteip'   => $this->_ip,
                            'challenge'  => $challengeField,
                            'response'   => $responseField);

        /* Make the POST and return the response */
        return $httpClient->setUri(self::VERIFY_SERVER)
                          ->setParameterPost($postParams)
                          ->request( Client ::POST);
    }

    /**
     * Verify the user input
     *
     * This method calls up the post method and returns a
     *  Response  object.
     *
     * @param string $challengeField
     * @param string $responseField
     * @return  Response 
     */
    public function verify($challengeField, $responseField)
    {
        $response = $this->_post($challengeField, $responseField);

        return new  Response (null, null, $response);
    }
}
