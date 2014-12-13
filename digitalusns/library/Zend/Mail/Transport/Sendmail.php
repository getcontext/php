<?php

namespace Zend\Mail\Transport;


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
 * @package    \Zend\Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Sendmail.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * @see  MailTransportAbstract 
 */
require_once 'Zend/Mail/Transport/Abstract.php';


/**
 * Class \for sending eMails via the PHP internal mail() function
 *
 * @category   Zend
 * @package    \Zend\Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Mail\Transport\Exception as MailTransportException;
use Zend\Mail\Transport\TransportAbstract as MailTransportAbstract;




class  Sendmail  extends  MailTransportAbstract 
{
    /**
     * Subject
     * @var string
     * @access public
     */
    public $subject = null;


    /**
     * Config options \for sendmail parameters
     *
     * @var string
     */
    public $parameters;


    /**
     * EOL character string
     * @var string
     * @access public
     */
    public $EOL = PHP_EOL;


    /**
     * Constructor.
     *
     * @param  string $parameters OPTIONAL (Default: null)
     * @return void
     */
    public function __construct($parameters = null)
    {
        $this->parameters = $parameters;
    }


    /**
     * Send mail using PHP native mail()
     *
     * @access public
     * @return void
     * @throws  MailTransportException  on mail() failure
     */
    public function _sendMail()
    {
        if ($this->parameters === null) {
            $result = mail(
                $this->recipients,
                $this->_mail->getSubject(),
                $this->body,
                $this->header);
        } else {
            $result = mail(
                $this->recipients,
                $this->_mail->getSubject(),
                $this->body,
                $this->header,
                $this->parameters);
        }
        if (!$result) {
            /**
             * @see  MailTransportException 
             */
            require_once 'Zend/Mail/Transport/Exception.php';
            throw new  MailTransportException ('Unable to send mail');
        }
    }


    /**
     * Format and fix headers
     *
     * mail() uses its $to and $subject arguments to set the To: and Subject:
     * headers, respectively. This method strips those out as a sanity check to
     * prevent duplicate header entries.
     *
     * @access  protected
     * @param   array $headers
     * @return  void
     * @throws   MailTransportException 
     */
    protected function _prepareHeaders($headers)
    {
        if (!$this->_mail) {
            /**
             * @see  MailTransportException 
             */
            require_once 'Zend/Mail/Transport/Exception.php';
            throw new  MailTransportException ('_prepareHeaders requires a registered \Zend\Mail object');
        }

        // mail() uses its $to parameter to set the To: header, and the $subject
        // parameter to set the Subject: header. We need to strip them out.
        if (0 === strpos(PHP_OS, 'WIN')) {
            // If the current recipients list is empty, throw an error
            if (empty($this->recipients)) {
                /**
                 * @see  MailTransportException 
                 */
                require_once 'Zend/Mail/Transport/Exception.php';
                throw new  MailTransportException ('Missing To addresses');
            }
        } else {
            // All others, simply grab the recipients and unset the To: header
            if (!isset($headers['To'])) {
                /**
                 * @see  MailTransportException 
                 */
                require_once 'Zend/Mail/Transport/Exception.php';
                throw new  MailTransportException ('Missing To header');
            }

            unset($headers['To']['append']);
            $this->recipients = implode(',', $headers['To']);
        }

        // Remove recipient header
        unset($headers['To']);

        // Remove subject header, if present
        if (isset($headers['Subject'])) {
            unset($headers['Subject']);
        }

        // Prepare headers
        parent::_prepareHeaders($headers);
    }

}

