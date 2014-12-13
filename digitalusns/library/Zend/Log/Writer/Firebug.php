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
 * @package     Log 
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Log  */
require_once 'Zend/Log.php';

/**  LogException  */
require_once 'Zend/Log/Exception.php';

/**  LogWriterAbstract  */
require_once 'Zend/Log/Writer/Abstract.php';

/**  FirePhp  */
require_once 'Zend/Wildfire/Plugin/FirePhp.php';

/**
 * Writes log messages to the Firebug Console via FirePHP.
 * 
 * @category   Zend
 * @package     Log 
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Wildfire\Plugin\FirePhp as FirePhp;
use Zend\Log\Writer\WriterAbstract as LogWriterAbstract;
use Zend\Log\Exception as LogException;
use Zend\Log as Log;




class  Firebug  extends  LogWriterAbstract 
{

    /**
     * Maps logging priorities to logging display styles
     * @var array
     */
    protected $_priorityStyles = array( Log ::EMERG  =>  FirePhp ::ERROR,
                                        Log ::ALERT  =>  FirePhp ::ERROR,
                                        Log ::CRIT   =>  FirePhp ::ERROR,
                                        Log ::ERR    =>  FirePhp ::ERROR,
                                        Log ::WARN   =>  FirePhp ::WARN,
                                        Log ::NOTICE =>  FirePhp ::INFO,
                                        Log ::INFO   =>  FirePhp ::INFO,
                                        Log ::DEBUG  =>  FirePhp ::LOG);
    
    /**
     * The default logging style \for un-mapped priorities
     * @var string
     */    
    protected $_defaultPriorityStyle =  FirePhp ::LOG;
    
    /**
     * Flag indicating whether the log writer is enabled
     * @var boolean
     */
    protected $_enabled = true;
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        if (php_sapi_name()=='cli') {
            $this->setEnabled(false);
        }
    }
    
    /**
     * Enable or disable the log writer.
     * 
     * @param boolean $enabled Set to TRUE to enable the log writer 
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->_enabled;
        $this->_enabled = $enabled;
        return $previous;
    }
    
    /**
     * Determine if the log writer is enabled.
     * 
     * @return boolean Returns TRUE if the log writer is enabled.
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }
    
    /**
     * Set the default display style \for user-defined priorities
     * 
     * @param string $style The default log display style
     * @return string Returns previous default log display style
     */    
    public function setDefaultPriorityStyle($style)
    {
        $previous = $this->_defaultPriorityStyle;
        $this->_defaultPriorityStyle = $style;
        return $previous;
    }
    
    /**
     * Get the default display style \for user-defined priorities
     * 
     * @return string Returns the default log display style
     */    
    public function getDefaultPriorityStyle()
    {
        return $this->_defaultPriorityStyle;
    }
    
    /**
     * Set a display style \for a logging priority
     * 
     * @param int $priority The logging priority
     * @param string $style The logging display style
     * @return string|boolean The previous logging display style if defined or TRUE otherwise
     */
    public function setPriorityStyle($priority, $style)
    {
        $previous = true;
        if (array_key_exists($priority,$this->_priorityStyles)) {
            $previous = $this->_priorityStyles[$priority];
        }
        $this->_priorityStyles[$priority] = $style;
        return $previous;
    }

    /**
     * Get a display style \for a logging priority
     * 
     * @param int $priority The logging priority
     * @return string|boolean The logging display style if defined or FALSE otherwise
     */
    public function getPriorityStyle($priority)
    {
        if (array_key_exists($priority,$this->_priorityStyles)) {
            return $this->_priorityStyles[$priority];
        }
        return false;
    }

    /**
     * Formatting is not possible on this writer
     *
     * @return void
     */
    public function setFormatter($formatter)
    {
        require_once 'Zend/Log/Exception.php';
        throw new  LogException (get_class() . ' does not support formatting');
    }

    /**
     * Log a message to the Firebug Console.
     *
     * @param array $event The event data
     * @return void
     */
    protected function _write($event)
    {
        if (!$this->getEnabled()) {
            return;
        }
      
        if (array_key_exists($event['priority'],$this->_priorityStyles)) {
            $type = $this->_priorityStyles[$event['priority']];
        } else {
            $type = $this->_defaultPriorityStyle;
        }
        
         FirePhp ::getInstance()->send($event['message'], null, $type);
    }
}
