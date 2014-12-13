<?php

namespace Zend\Mail\Protocol;


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
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Pop3.php 9098 2008-03-30 19:29:10Z thomas $
 */


/**
 * @category   Zend
 * @package    \Zend\Mail
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Mail\Protocol\Exception as MailProtocolException;




class  Pop3 
{
    /**
     * saves if server supports top
     * @var null|bool
     */
    public $hasTop = null;

    /**
     * socket to pop3
     * @var null|resource
     */
    protected $_socket;

    /**
     * greeting timestamp \for apop
     * @var null|string
     */
    protected $_timestamp;


    /**
     * Public constructor
     *
     * @param  string      $host  hostname \of IP address \of POP3 server, if given connect() is called
     * @param  int|null    $port  port \of POP3 server, null \for default (110 or 995 \for ssl)
     * @param  bool|string $ssl   use ssl? 'SSL', 'TLS' or false
     * @throws  MailProtocolException 
     */
    public function __construct($host = '', $port = null, $ssl = false)
    {
        if ($host) {
            $this->connect($host, $port, $ssl);
        }
    }


    /**
     * Public destructor
     */
    public function __destruct()
    {
        $this->logout();
    }


    /**
     * Open connection to POP3 server
     *
     * @param  string      $host  hostname \of IP address \of POP3 server
     * @param  int|null    $port  \of POP3 server, default is 110 (995 \for ssl)
     * @param  string|bool $ssl   use 'SSL', 'TLS' or false
     * @return string welcome message
     * @throws  MailProtocolException 
     */
    public function connect($host, $port = null, $ssl = false)
    {
        if ($ssl == 'SSL') {
            $host = 'ssl://' . $host;
        }

        if ($port === null) {
            $port = $ssl == 'SSL' ? 995 : 110;
        }

        $this->_socket = @fsockopen($host, $port);
        if (!$this->_socket) {
            /**
             * @see  MailProtocolException 
             */
            require_once 'Zend/Mail/Protocol/Exception.php';
            throw new  MailProtocolException ('cannot connect to host');
        }

        $welcome = $this->readResponse();

        strtok($welcome, '<');
        $this->_timestamp = strtok('>');
        if (!strpos($this->_timestamp, '@')) {
            $this->_timestamp = null;
        } else {
            $this->_timestamp = '<' . $this->_timestamp . '>';
        }

        if ($ssl === 'TLS') {
            $this->request('STLS');
            $result = stream_socket_enable_crypto($this->_socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$result) {
                /**
                 * @see  MailProtocolException 
                 */
                require_once 'Zend/Mail/Protocol/Exception.php';
                throw new  MailProtocolException ('cannot enable TLS');
            }
        }

        return $welcome;
    }


    /**
     * Send a request
     *
     * @param string $request your request without newline
     * @return null
     * @throws  MailProtocolException 
     */
    public function sendRequest($request)
    {
        $result = @fputs($this->_socket, $request . "\r\n");
        if (!$result) {
            /**
             * @see  MailProtocolException 
             */
            require_once 'Zend/Mail/Protocol/Exception.php';
            throw new  MailProtocolException ('send failed - connection closed?');
        }
    }


    /**
     * read a response
     *
     * @param  boolean $multiline response has multiple lines and should be read until "<nl>.<nl>"
     * @return string response
     * @throws  MailProtocolException 
     */
    public function readResponse($multiline = false)
    {
        $result = @fgets($this->_socket);
        if (!is_string($result)) {
            /**
             * @see  MailProtocolException 
             */
            require_once 'Zend/Mail/Protocol/Exception.php';
            throw new  MailProtocolException ('read failed - connection closed?');
        }

        $result = trim($result);
        if (strpos($result, ' ')) {
            list($status, $message) = explode(' ', $result, 2);
        } else {
            $status = $result;
            $message = '';
        }

        if ($status != '+OK') {
            /**
             * @see  MailProtocolException 
             */
            require_once 'Zend/Mail/Protocol/Exception.php';
            throw new  MailProtocolException ('last request failed');
        }

        if ($multiline) {
            $message = '';
            $line = fgets($this->_socket);
            while ($line && trim($line) != '.') {
                $message .= $line;
                $line = fgets($this->_socket);
            };
        }

        return $message;
    }


    /**
     * Send request and get resposne
     *
     * @see sendRequest(), readResponse()
     *
     * @param  string $request    request
     * @param  bool   $multiline  multiline response?
     * @return string             result from readResponse()
     * @throws  MailProtocolException 
     */
    public function request($request, $multiline = false)
    {
        $this->sendRequest($request);
        return $this->readResponse($multiline);
    }


    /**
     * End communication with POP3 server (also closes socket)
     *
     * @return null
     */
    public function logout()
    {
        if (!$this->_socket) {
            return;
        }

        try {
            $this->request('QUIT');
        } catch ( MailProtocolException  $e) {
            // ignore error - we're closing the socket anyway
        }

        fclose($this->_socket);
        $this->_socket = null;
    }


    /**
     * Get capabilities from POP3 server
     *
     * @return array list \of capabilities
     * @throws  MailProtocolException 
     */
    public function capa()
    {
        $result = $this->request('CAPA', true);
        return explode("\n", $result);
    }


    /**
     * Login to POP3 server. Can use APOP
     *
     * @param  string $user      username
     * @param  string $password  password
     * @param  bool   $try_apop  should APOP be tried?
     * @return void
     * @throws  MailProtocolException 
     */
    public function login($user, $password, $tryApop = true)
    {
        if ($tryApop && $this->_timestamp) {
            try {
                $this->request("APOP $user " . md5($this->_timestamp . $password));
                return;
            } catch ( MailProtocolException  $e) {
                // ignore
            }
        }

        $result = $this->request("USER $user");
        $result = $this->request("PASS $password");
    }


    /**
     * Make STAT call \for message count and size sum
     *
     * @param  int $messages  out parameter with count \of messages
     * @param  int $octets    out parameter with size in octects \of messages
     * @return void
     * @throws  MailProtocolException 
     */
    public function status(&$messages, &$octets)
    {
        $messages = 0;
        $octets = 0;
        $result = $this->request('STAT');

        list($messages, $octets) = explode(' ', $result);
    }


    /**
     * Make LIST call \for size \of message(s)
     *
     * @param  int|null $msgno number \of message, null \for all
     * @return int|array size \of given message or list with array(num => size)
     * @throws  MailProtocolException 
     */
    public function getList($msgno = null)
    {
        if ($msgno !== null) {
            $result = $this->request("LIST $msgno");

            list(, $result) = explode(' ', $result);
            return (int)$result;
        }

        $result = $this->request('LIST', true);
        $messages = array();
        $line = strtok($result, "\n");
        while ($line) {
            list($no, $size) = explode(' ', trim($line));
            $messages[(int)$no] = (int)$size;
            $line = strtok("\n");
        }

        return $messages;
    }


    /**
     * Make UIDL call \for getting a uniqueid
     *
     * @param  int|null $msgno number \of message, null \for all
     * @return string|array uniqueid \of message or list with array(num => uniqueid)
     * @throws  MailProtocolException 
     */
    public function uniqueid($msgno = null)
    {
        if ($msgno !== null) {
            $result = $this->request("UIDL $msgno");

            list(, $result) = explode(' ', $result);
            return $result;
        }

        $result = $this->request('UIDL', true);

        $result = explode("\n", $result);
        $messages = array();
        foreach ($result as $line) {
            if (!$line) {
                continue;
            }
            list($no, $id) = explode(' ', trim($line), 2);
            $messages[(int)$no] = $id;
        }

        return $messages;

    }


    /**
     * Make TOP call \for getting headers and maybe some body lines
     * This method also sets hasTop - before it it's not known if top is supported
     *
     * The fallback makes normale RETR call, which retrieves the whole message. Additional
     * lines are not removed.
     *
     * @param  int  $msgno    number \of message
     * @param  int  $lines    number \of wanted body lines (empty line is inserted after header lines)
     * @param  bool $fallback fallback with full retrieve if top is not supported
     * @return string message headers with wanted body lines
     * @throws  MailProtocolException 
     */
    public function top($msgno, $lines = 0, $fallback = false)
    {
        if ($this->hasTop === false) {
            if ($fallback) {
                return $this->retrieve($msgno);
            } else {
                /**
                 * @see  MailProtocolException 
                 */
                require_once 'Zend/Mail/Protocol/Exception.php';
                throw new  MailProtocolException ('top not supported and no fallback wanted');
            }
        }
        $this->hasTop = true;

        $lines = (!$lines || $lines < 1) ? 0 : (int)$lines;

        try {
            $result = $this->request("TOP $msgno $lines", true);
        } catch ( MailProtocolException  $e) {
            $this->hasTop = false;
            if ($fallback) {
                $result = $this->retrieve($msgno);
            } else {
                throw $e;
            }
        }

        return $result;
    }


    /**
     * Make a RETR call \for retrieving a full message with headers and body
     *
     * @deprecated since 1.1.0; this method has a typo - please use retrieve()
     * @param  int $msgno  message number
     * @return string message
     * @throws  MailProtocolException 
     */
    public function retrive($msgno)
    {
        return $this->retrieve($msgno);
    }


    /**
     * Make a RETR call \for retrieving a full message with headers and body
     *
     * @param  int $msgno  message number
     * @return string message
     * @throws  MailProtocolException 
     */
    public function retrieve($msgno)
    {
        $result = $this->request("RETR $msgno", true);
        return $result;
    }

    /**
     * Make a NOOP call, maybe needed \for keeping the server happy
     *
     * @return null
     * @throws  MailProtocolException 
     */
    public function noop()
    {
        $this->request('NOOP');
    }


    /**
     * Make a DELE count to remove a message
     *
     * @return null
     * @throws  MailProtocolException 
     */
    public function delete($msgno)
    {
        $this->request("DELE $msgno");
    }


    /**
     * Make RSET call, which rollbacks delete requests
     *
     * @return null
     * @throws  MailProtocolException 
     */
    public function undelete()
    {
        $this->request('RSET');
    }
}
