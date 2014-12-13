<?php

namespace Zend\Log\Formatter;


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
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Simple.php 10726 2008-08-06 16:38:09Z cadorn $
 */

/**  LogFormatterInterface  */
require_once 'Zend/Log/Formatter/Interface.php';

/**  LogException  */
require_once 'Zend/Log/Exception.php';

/**
 * @category   Zend
 * @package    \Zend\Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Simple.php 10726 2008-08-06 16:38:09Z cadorn $
 */


use Zend\Log\Formatter\FormatterInterface as LogFormatterInterface;
use Zend\Log\Exception as LogException;




class  Simple  implements  LogFormatterInterface 
{
    /**
     * @var string
     */
    protected $_format;

    const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%): %message%';

    /**
     * Class constructor
     *
     * @param  null|string  $format  Format specifier \for log messages
     * @throws  LogException 
     */
    public function __construct($format = null)
    {
        if ($format === null) {
            $format = self::DEFAULT_FORMAT . PHP_EOL;
        }

        if (! is_string($format)) {
            throw new  LogException ('Format must be a string');
        }

        $this->_format = $format;
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param  array    $event    event data
     * @return string             formatted line to write to the log
     */
    public function format($event)
    {
        $output = $this->_format;
        foreach ($event as $name => $value) {

            if ((is_object($value) && !method_exists($value,'__toString'))
                || is_array($value)) {

                $value = gettype($value);  
            }

            $output = str_replace("%$name%", $value, $output);
        }
        return $output;
    }

}
