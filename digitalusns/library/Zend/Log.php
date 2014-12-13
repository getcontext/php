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
 * @package     Log 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Log.php 9382 2008-05-05 18:55:55Z doctorrock83 $
 */

/**  Priority  */
require_once 'Zend/Log/Filter/Priority.php';

/**
 * @category   Zend
 * @package     Log 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Log.php 9382 2008-05-05 18:55:55Z doctorrock83 $
 */


use Zend\Log\Filter\FilterInterface as LogFilterInterface;
use Zend\Log\Writer\WriterAbstract as LogWriterAbstract;
use Zend\Log\Filter\Priority as Priority;
use Zend\Log\Exception as LogException;




class  Log 
{
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages

    /**
     * @var array \of priorities where the keys are the
     * priority numbers and the values are the priority names
     */
    protected $_priorities = array();

    /**
     * @var array \of  LogWriterAbstract 
     */
    protected $_writers = array();

    /**
     * @var array \of  LogFilterInterface 
     */
    protected $_filters = array();

    /**
     * @var array \of extra log event
     */
    protected $_extras = array();

    /**
     * Class constructor.  Create a new logger
     *
     * @param  LogWriterAbstract |null  $writer  default writer
     */
    public function __construct( LogWriterAbstract  $writer = null)
    {
        $r = new \ReflectionClass($this);
        $this->_priorities = array_flip($r->getConstants());

        if ($writer !== null) {
            $this->addWriter($writer);
        }
    }

    /**
     * Class destructor.  Shutdown log writers
     *
     * @return void
     */
    public function __destruct()
    {
        foreach($this->_writers as $writer) {
            $writer->shutdown();
        }
    }

    /**
     * Undefined method handler allows a shortcut:
     *   $log->priorityName('message')
     *     instead \of
     *   $log->log('message',  Log ::PRIORITY_NAME)
     *
     * @param  string  $method  priority name
     * @param  string  $params  message to log
     * @return void
     * @throws  LogException 
     */
    public function __call($method, $params)
    {
        $priority = strtoupper($method);
        if (($priority = array_search($priority, $this->_priorities)) !== false) {
            $this->log(array_shift($params), $priority);
        } else {
            /** @see  LogException  */
            require_once 'Zend/Log/Exception.php';
            throw new  LogException ('Bad log priority');
        }
    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority \of message
     * @return void
     * @throws  LogException 
     */
    public function log($message, $priority)
    {
        // sanity checks
        if (empty($this->_writers)) {
            /** @see  LogException  */
            require_once 'Zend/Log/Exception.php';
            throw new  LogException ('No writers were added');
        }

        if (! isset($this->_priorities[$priority])) {
            /** @see  LogException  */
            require_once 'Zend/Log/Exception.php';
            throw new  LogException ('Bad log priority');
        }

        // pack into event required by filters and writers
        $event = array_merge(array('timestamp'    => date('c'),
                                    'message'      => $message,
                                    'priority'     => $priority,
                                    'priorityName' => $this->_priorities[$priority]),
                              $this->_extras);

        // abort if rejected by the global filters
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // send to each writer
        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
    }

    /**
     * Add a custom priority
     *
     * @param  string   $name      Name \of priority
     * @param  integer  $priority  Numeric priority
     * @throws  Log _InvalidArgumentException
     */
    public function addPriority($name, $priority)
    {
        // Priority names must be uppercase \for predictability.
        $name = strtoupper($name);

        if (isset($this->_priorities[$priority])
            || array_search($name, $this->_priorities)) {
            /** @see  LogException  */
            require_once 'Zend/Log/Exception.php';
            throw new  LogException ('Existing priorities cannot be overwritten');
        }

        $this->_priorities[$priority] = $name;
    }

    /**
     * Add a filter that will be applied before all log writers.
     * Before a message will be received by any \of the writers, it
     * must be accepted by all filters added with this method.
     *
     * @param  int| LogFilterInterface  $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new  Priority ($filter);
        } elseif(!is_object($filter) || ! $filter instanceof  LogFilterInterface ) {
            /** @see  LogException  */
            require_once 'Zend/Log/Exception.php';
            throw new  LogException ('Invalid filter provided');
        }

        $this->_filters[] = $filter;
    }

    /**
     * Add a writer.  A writer is responsible \for taking a log
     * message and writing it out to storage.
     *
     * @param   LogWriterAbstract  $writer
     * @return void
     */
    public function addWriter( LogWriterAbstract  $writer)
    {
        $this->_writers[] = $writer;
    }

    /**
     * Set an extra item to pass to the log writers.
     *
     * @param  $name    Name \of the field
     * @param  $value   Value \of the field
     * @return void
     */
    public function setEventItem($name, $value) {
        $this->_extras = array_merge($this->_extras, array($name => $value));
    }

}
