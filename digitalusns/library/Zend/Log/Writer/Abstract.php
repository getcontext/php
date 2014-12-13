<?php

namespace Zend\Log\Writer;


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
 * @package    \Zend\Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 8064 2008-02-16 10:58:39Z thomas $
 */

/**  Priority  */
require_once 'Zend/Log/Filter/Priority.php';

/** \Zend\Log\Exception */
require_once 'Zend/Log/Exception.php';

/**
 * @category   Zend
 * @package    \Zend\Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 8064 2008-02-16 10:58:39Z thomas $
 */



use Zend\Log\Formatter\FormatterInterface as LogFormatterInterface;
use Zend\Log\Filter\FilterInterface as LogFilterInterface;
use Zend\Log\Filter\Priority as Priority;



abstract class  WriterAbstract 
{
    /**
     * @var array \of  LogFilterInterface 
     */
    protected $_filters = array();

    /**
     * Formats the log message before writing.
     * @var  LogFormatterInterface 
     */
    protected $_formatter;

    /**
     * Add a filter specific to this writer.
     *
     * @param   LogFilterInterface   $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (is_integer($filter)) {
            $filter = new  Priority ($filter);
        }

        $this->_filters[] = $filter;
    }

    /**
     * Log a message to this writer.
     *
     * @param  array     $event  log data event
     * @return void
     */
    public function write($event)
    {
        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        // exception occurs on error
        $this->_write($event);
    }

    /**
     * Set a new formatter \for this writer
     *
     * @param   LogFormatterInterface  $formatter
     * @return void
     */
    public function setFormatter($formatter) {
        $this->_formatter = $formatter;
    }

    /**
     * Perform shutdown activites such as closing open resources
     *
     * @return void
     */
    public function shutdown()
    {}

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
    abstract protected function _write($event);

}
