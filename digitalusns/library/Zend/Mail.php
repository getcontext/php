<?php

namespace Zend;


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
 * @package     Mail 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Mail.php 9222 2008-04-14 13:48:41Z yoshida@zend.co.jp $
 */


/**
 * @see  MailTransportAbstract 
 */
require_once 'Zend/Mail/Transport/Abstract.php';

/**
 * @see  Mime 
 */
require_once 'Zend/Mime.php';

/**
 * @see  MailTransportAbstract 
 */
require_once 'Zend/Mail/Transport/Abstract.php';

/**
 * @see  Message 
 */
require_once 'Zend/Mime/Message.php';

/**
 * @see  Part 
 */
require_once 'Zend/Mime/Part.php';


/**
 * Class \for sending an email.
 *
 * @category   Zend
 * @package     Mail 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Mail\Transport\TransportAbstract as MailTransportAbstract;
use Zend\Mail\Transport\Sendmail as Sendmail;
use Zend\Mail\Exception as MailException;
use Zend\Mime\Message as Message;
use Zend\Mime\Part as Part;
use Zend\Mime as Mime;
use Zend\Date as Date;




class  Mail  extends  Message 
{
    /**#@+
     * @access protected
     */

    /**
     * @var  MailTransportAbstract 
     * @static
     */
    protected static $_defaultTransport = null;

    /**
     * Mail character set
     * @var string
     */
    protected $_charset = null;

    /**
     * Mail headers
     * @var array
     */
    protected $_headers = array();

    /**
     * From: address
     * @var string
     */
    protected $_from = null;

    /**
     * To: addresses
     * @var array
     */
    protected $_to = array();

    /**
     * Array \of all recipients
     * @var array
     */
    protected $_recipients = array();

    /**
     * Return-Path header
     * @var string
     */
    protected $_returnPath = null;

    /**
     * Subject: header
     * @var string
     */
    protected $_subject = null;

    /**
     * Date: header
     * @var string
     */
    protected $_date = null;

    /**
     * text/plain MIME part
     * @var false| Part 
     */
    protected $_bodyText = false;

    /**
     * text/html MIME part
     * @var false| Part 
     */
    protected $_bodyHtml = false;

    /**
     * MIME boundary string
     * @var string
     */
    protected $_mimeBoundary = null;

    /**
     * Content type \of the message
     * @var string
     */
    protected $_type = null;

    /**#@-*/

    /**
     * Flag: whether or not email has attachments
     * @var boolean
     */
    public $hasAttachments = false;


    /**
     * Sets the default mail transport \for all following uses \of
     *  Mail ::send();
     *
     * @todo Allow passing a string to indicate the transport to load
     * @todo Allow passing in optional options \for the transport to load
     * @param   MailTransportAbstract  $transport
     */
    public static function setDefaultTransport( MailTransportAbstract  $transport)
    {
        self::$_defaultTransport = $transport;
    }

    /**
     * Public constructor
     *
     * @param string $charset
     */
    public function __construct($charset = 'iso-8859-1')
    {
        $this->_charset = $charset;
    }

    /**
     * Return charset string
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * Set content type
     *
     * Should only be used \for manually setting multipart content types.
     *
     * @param  string $type Content type
     * @return  Mail  Implements fluent interface
     * @throws  MailException  \for types not supported by  Mime 
     */
    public function setType($type)
    {
        $allowed = array(
             Mime ::MULTIPART_ALTERNATIVE,
             Mime ::MULTIPART_MIXED,
             Mime ::MULTIPART_RELATED,
        );
        if (!in_array($type, $allowed)) {
            /**
             * @see  MailException 
             */
            require_once 'Zend/Mail/Exception.php';
            throw new  MailException ('Invalid content type "' . $type . '"');
        }

        $this->_type = $type;
        return $this;
    }

    /**
     * Get content type \of the message
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set an arbitrary mime boundary \for the message
     *
     * If not set,  Mime  will generate one.
     *
     * @param  string    $boundary
     * @return  Mail  Provides fluent interface
     */
    public function setMimeBoundary($boundary)
    {
        $this->_mimeBoundary = $boundary;

        return $this;
    }

    /**
     * Return the boundary string used \for the message
     *
     * @return string
     */
    public function getMimeBoundary()
    {
        return $this->_mimeBoundary;
    }

    /**
     * Sets the text body \for the message.
     *
     * @param  string $txt
     * @param  string $charset
     * @param  string $encoding
     * @return  Mail  Provides fluent interface
    */
    public function setBodyText($txt, $charset = null, $encoding =  Mime ::ENCODING_QUOTEDPRINTABLE)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new  Part ($txt);
        $mp->encoding = $encoding;
        $mp->type =  Mime ::TYPE_TEXT;
        $mp->disposition =  Mime ::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_bodyText = $mp;

        return $this;
    }

    /**
     * Return text body  Part  or string
     *
     * @param  bool textOnly Whether to return just the body text content or the MIME part; defaults to false, the MIME part
     * @return false| Part |string
     */
    public function getBodyText($textOnly = false)
    {
        if ($textOnly && $this->_bodyText) {
            $body = $this->_bodyText;
            return $body->getContent();
        }

        return $this->_bodyText;
    }

    /**
     * Sets the HTML body \for the message
     *
     * @param  string    $html
     * @param  string    $charset
     * @param  string    $encoding
     * @return  Mail  Provides fluent interface
     */
    public function setBodyHtml($html, $charset = null, $encoding =  Mime ::ENCODING_QUOTEDPRINTABLE)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new  Part ($html);
        $mp->encoding = $encoding;
        $mp->type =  Mime ::TYPE_HTML;
        $mp->disposition =  Mime ::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->_bodyHtml = $mp;

        return $this;
    }

    /**
     * Return  Part  representing body HTML
     *
     * @param  bool $htmlOnly Whether to return the body HTML only, or the MIME part; defaults to false, the MIME part
     * @return false| Part |string
     */
    public function getBodyHtml($htmlOnly = false)
    {
        if ($htmlOnly && $this->_bodyHtml) {
            $body = $this->_bodyHtml;
            return $body->getContent();
        }

        return $this->_bodyHtml;
    }

    /**
     * Adds an existing attachment to the mail message
     *
     * @param   Part  $attachment
     * @return  Mail  Provides fluent interface
     */
    public function addAttachment( Part  $attachment)
    {
        $this->addPart($attachment);
        $this->hasAttachments = true;

        return $this;
    }

    /**
     * Creates a  Part  attachment
     *
     * Attachment is automatically added to the mail object after creation. The
     * attachment object is returned to allow \for further manipulation.
     *
     * @param  string         $body
     * @param  string         $mimeType
     * @param  string         $disposition
     * @param  string         $encoding
     * @param  string         $filename OPTIONAL A filename \for the attachment
     * @return  Part  Newly created  Part  object (to allow
     * advanced settings)
     */
    public function createAttachment($body,
                                     $mimeType    =  Mime ::TYPE_OCTETSTREAM,
                                     $disposition =  Mime ::DISPOSITION_ATTACHMENT,
                                     $encoding    =  Mime ::ENCODING_BASE64,
                                     $filename    = null)
    {

        $mp = new  Part ($body);
        $mp->encoding = $encoding;
        $mp->type = $mimeType;
        $mp->disposition = $disposition;
        $mp->filename = $filename;

        $this->addAttachment($mp);

        return $mp;
    }

    /**
     * Return a count \of message parts
     *
     * @return integer
     */
    public function getPartCount()
    {
        return count($this->_parts);
    }

    /**
     * Encode header fields
     *
     * Encodes header content according to RFC1522 if it contains non-printable
     * characters.
     *
     * @param  string $value
     * @return string
     */
    protected function _encodeHeader($value)
    {
      if ( Mime ::isPrintable($value)) {
          return $value;
      } else {
          $quotedValue =  Mime ::encodeQuotedPrintable($value);
          $quotedValue = str_replace(array('?', ' ', '_'), array('=3F', '=20', '=5F'), $quotedValue);
          return '=?' . $this->_charset . '?Q?' . $quotedValue . '?=';
      }
    }

    /**
     * Add a header to the message
     *
     * Adds a header to this message. If append is true and the header already
     * exists, raises a flag indicating that the header should be appended.
     *
     * @param string  $headerName
     * @param string  $value
     * @param bool $append
     */
    protected function _storeHeader($headerName, $value, $append = false)
    {
// ??        $value = strtr($value,"\r\n\t",'???');
        if (isset($this->_headers[$headerName])) {
            $this->_headers[$headerName][] = $value;
        } else {
            $this->_headers[$headerName] = array($value);
        }

        if ($append) {
            $this->_headers[$headerName]['append'] = true;
        }

    }

    /**
     * Add a recipient
     *
     * @param string $email
     * @param boolean $to
     */
    protected function _addRecipient($email, $to = false)
    {
        // prevent duplicates
        $this->_recipients[$email] = 1;

        if ($to) {
            $this->_to[] = $email;
        }
    }

    /**
     * Helper function \for adding a recipient and the corresponding header
     *
     * @param string $headerName
     * @param string $name
     * @param string $email
     */
    protected function _addRecipientAndHeader($headerName, $name, $email)
    {
        $email = strtr($email,"\r\n\t",'???');
        $this->_addRecipient($email, ('To' == $headerName) ? true : false);
        if ($name != '') {
            $name = '"' . $this->_encodeHeader($name) . '" ';
        }

        $this->_storeHeader($headerName, $name .'<'. $email . '>', true);
    }

    /**
     * Adds To-header and recipient
     *
     * @param  string $email
     * @param  string $name
     * @return  Mail  Provides fluent interface
     */
    public function addTo($email, $name='')
    {
        $this->_addRecipientAndHeader('To', $name, $email);
        return $this;
    }

    /**
     * Adds Cc-header and recipient
     *
     * @param  string    $email
     * @param  string    $name
     * @return  Mail  Provides fluent interface
     */
    public function addCc($email, $name='')
    {
        $this->_addRecipientAndHeader('Cc', $name, $email);
        return $this;
    }

    /**
     * Adds Bcc recipient
     *
     * @param  string    $email
     * @return  Mail  Provides fluent interface
     */
    public function addBcc($email)
    {
        $this->_addRecipientAndHeader('Bcc', '', $email);
        return $this;
    }

    /**
     * Return list \of recipient email addresses
     *
     * @return array (\of strings)
     */
    public function getRecipients()
    {
        return array_keys($this->_recipients);
    }

    /**
     * Sets From-header and sender \of the message
     *
     * @param  string    $email
     * @param  string    $name
     * @return  Mail  Provides fluent interface
     * @throws  MailException  if called subsequent times
     */
    public function setFrom($email, $name = '')
    {
        if ($this->_from === null) {
            $email = strtr($email,"\r\n\t",'???');
            $this->_from = $email;
            $this->_storeHeader('From', $this->_encodeHeader('"'.$name.'"').' <'.$email.'>', true);
        } else {
            /**
             * @see  MailException 
             */
            require_once 'Zend/Mail/Exception.php';
            throw new  MailException ('From Header set twice');
        }
        return $this;
    }

    /**
     * Returns the sender \of the mail
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * Sets the Return-Path header \for an email
     *
     * @param  string    $email
     * @return  Mail  Provides fluent interface
     * @throws  MailException  if set multiple times
     */
    public function setReturnPath($email)
    {
        if ($this->_returnPath === null) {
            $email = strtr($email,"\r\n\t",'???');
            $this->_returnPath = $email;
            $this->_storeHeader('Return-Path', $email, false);
        } else {
            /**
             * @see  MailException 
             */
            require_once 'Zend/Mail/Exception.php';
            throw new  MailException ('Return-Path Header set twice');
        }
        return $this;
    }

    /**
     * Returns the current Return-Path address \for the email
     *
     * If no Return-Path header is set, returns the value \of {@link $_from}.
     *
     * @return string
     */
    public function getReturnPath()
    {
        if (null !== $this->_returnPath) {
            return $this->_returnPath;
        }

        return $this->_from;
    }

    /**
     * Sets the subject \of the message
     *
     * @param   string    $subject
     * @return   Mail  Provides fluent interface
     * @throws   MailException 
     */
    public function setSubject($subject)
    {
        if ($this->_subject === null) {
            $subject = strtr($subject,"\r\n\t",'???');
            $this->_subject = $this->_encodeHeader($subject);
            $this->_storeHeader('Subject', $this->_subject);
        } else {
            /**
             * @see  MailException 
             */
            require_once 'Zend/Mail/Exception.php';
            throw new  MailException ('Subject set twice');
        }
        return $this;
    }

    /**
     * Returns the encoded subject \of the message
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }
    
    /**
     * Sets Date-header
     *
     * @param  string    $date
     * @return  Mail  Provides fluent interface
     * @throws  MailException  if called subsequent times
     */
    public function setDate($date = null)
    {
        if ($this->_date === null) {
            if ($date === null) {
                $date = date('r');
            } else if (is_int($date)) {
                $date = date('r', $date);
            } else if (is_string($date)) {
            	$date = strtotime($date);
                if ($date === false || $date < 0) {
                    throw new  MailException ('String representations \of Date Header must be ' .
                                                  'strtotime()-compatible');
                }
                $date = date('r', $date);
            } else if ($date instanceof  Date ) {
                $date = $date->get( Date ::RFC_2822);
            } else {
                throw new  MailException (__METHOD__ . ' only accepts UNIX timestamps,  Date  objects, ' .
                                              ' and strtotime()-compatible strings');
            }
            $this->_date = $date;
            $this->_storeHeader('Date', $date);
        } else {
            throw new  MailException ('Date Header set twice');
        }
        return $this;
    }

    /**
     * Returns the formatted date \of the message
     *
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * Add a custom header to the message
     *
     * @param  string              $name
     * @param  string              $value
     * @param  boolean             $append
     * @return  Mail            Provides fluent interface
     * @throws  MailException  on attempts to create standard headers
     */
    public function addHeader($name, $value, $append = false)
    {
        if (in_array(strtolower($name), array('to', 'cc', 'bcc', 'from', 'subject', 'return-path', 'date'))) {
            /**
             * @see  MailException 
             */
            require_once 'Zend/Mail/Exception.php';
            throw new  MailException ('Cannot set standard header from addHeader()');
        }

        $value = strtr($value,"\r\n\t",'???');
        $value = $this->_encodeHeader($value);
        $this->_storeHeader($name, $value, $append);

        return $this;
    }

    /**
     * Return mail headers
     *
     * @return void
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param   MailTransportAbstract  $transport
     * @return  Mail                     Provides fluent interface
     */
    public function send($transport = null)
    {
        if ($transport === null) {
            if (! self::$_defaultTransport instanceof  MailTransportAbstract ) {
                require_once 'Zend/Mail/Transport/Sendmail.php';
                $transport = new  Sendmail ();
            } else {
                $transport = self::$_defaultTransport;
            }
        }

        if (is_null($this->_date)) {
            $this->setDate();
        }

        $transport->send($this);

        return $this;
    }

}
