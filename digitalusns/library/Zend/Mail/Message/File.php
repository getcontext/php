<?php

namespace Zend\Mail\Message;


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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Message.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 *  Part 
 */
require_once 'Zend/Mail/Part/File.php';

/**
 *  MailMessageInterface 
 */
require_once 'Zend/Mail/Message/Interface.php';

/**
 * @category   Zend
 * @package    \Zend\Mail
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Mail\Message\MessageInterface as MailMessageInterface;
use Zend\Mail\Part\File as MailPartFile;
use Zend\Mail\Part as Part;




class  File  extends  MailPartFile  implements  MailMessageInterface 
{
    /**
     * flags \for this message
     * @var array
     */
    protected $_flags = array();

    /**
     * Public constructor
     *
     * In addition to the parameters \of  Part ::__construct() this constructor supports:
     * - flags array with flags \for message, keys are ignored, use constants defined in \Zend\Mail\Storage
     *
     * @param  string $rawMessage  full message with or without headers
     * @throws \Zend\Mail\Exception
     */
    public function __construct(array $params)
    {
        if (!empty($params['flags'])) {
            // set key and value to the same value \for easy lookup
            $this->_flags = array_combine($params['flags'], $params['flags']);
        }
        
        parent::__construct($params);
    }

    /**
     * return toplines as found after headers
     *
     * @return string toplines
     */
    public function getTopLines()
    {
        return $this->_topLines;
    }

    /**
     * check if flag is set
     *
     * @param mixed $flag a flag name, use constants defined in \Zend\Mail\Storage
     * @return bool true if set, otherwise false
     */
    public function hasFlag($flag)
    {
        return isset($this->_flags[$flag]);
    }

    /**
     * get all set flags
     *
     * @return array array with flags, key and value are the same \for easy lookup
     */
    public function getFlags()
    {
        return $this->_flags;
    }
}
