<?php

namespace Zend\Db\Table\Rowset;


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
 * @package    \Zend\Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 5896 2007-07-27 20:04:24Z bkarwin $
 */

/**
 * @see  Loader 
 */
require_once 'Zend/Loader.php';

/**
 * @category   Zend
 * @package    \Zend\Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Db\Table\Rowset\Exception as DbTableRowsetException;
use Zend\Db\Table\TableAbstract as DbTableAbstract;
use Zend\Loader as Loader;



abstract class  RowsetAbstract  implements \SeekableIterator, \Countable
{
    /**
     * The original data \for each row.
     *
     * @var array
     */
    protected $_data = array();

    /**
     *  DbTableAbstract  object.
     *
     * @var  DbTableAbstract 
     */
    protected $_table;

    /**
     * Connected is true if we have a reference to a live
     *  DbTableAbstract  object.
     * This is false after the Rowset has been deserialized.
     *
     * @var boolean
     */
    protected $_connected = true;

    /**
     *  DbTableAbstract  class name.
     *
     * @var string
     */
    protected $_tableClass;

    /**
     * \Zend\Db\Table\Row\RowAbstract class name.
     *
     * @var string
     */
    protected $_rowClass = '\Zend\Db\Table\Row';

    /**
     * \Iterator pointer.
     *
     * @var integer
     */
    protected $_pointer = 0;

    /**
     * How many data rows there are.
     *
     * @var integer
     */
    protected $_count;

    /**
     * Collection \of instantiated \Zend\Db\Table\Row objects.
     *
     * @var array
     */
    protected $_rows = array();

    /**
     * @var boolean
     */
    protected $_stored = false;

    /**
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['table'])) {
            $this->_table      = $config['table'];
            $this->_tableClass = get_class($this->_table);
        }
        if (isset($config['rowClass'])) {
            $this->_rowClass   = $config['rowClass'];
        }
        @ Loader ::loadClass($this->_rowClass);
        if (isset($config['data'])) {
            $this->_data       = $config['data'];
        }
        if (isset($config['readOnly'])) {
            $this->_readOnly   = $config['readOnly'];
        }
        if (isset($config['stored'])) {
            $this->_stored     = $config['stored'];
        }

        // set the count \of rows
        $this->_count = count($this->_data);
        
        $this->init();
    }

    /**
     * Store data, class names, and state in serialized object
     *
     * @return array
     */
    public function __sleep()
    {
        return array('_data', '_tableClass', '_rowClass', '_pointer', '_count', '_rows', '_stored',
                     '_readOnly');
    }

    /**
     * Setup to do on wakeup.
     * A de-serialized Rowset should not be assumed to have access to a live
     * database connection, so set _connected = false.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_connected = false;
    }

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step \of object instantiation.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Return the connected state \of the rowset.
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->_connected;
    }

    /**
     * Returns the table object, or null if this is disconnected rowset
     *
     * @return  DbTableAbstract 
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Set the table object, to re-establish a live connection
     * to the database \for a Rowset that has been de-serialized.
     *
     * @param  DbTableAbstract  $table
     * @return boolean
     * @throws \Zend\Db\Table\Row\Exception
     */
    public function setTable( DbTableAbstract  $table)
    {
        $this->_table = $table;
        $this->_connected = false;
        // @todo This works only if we have iterated through
        // the result set once to instantiate the rows.
        foreach ($this as $row) {
            $connected = $row->setTable($table);
            if ($connected == true) {
                $this->_connected = true;
            }
        }
        return $this->_connected;
    }

    /**
     * Query the class name \of the Table object \for which this
     * Rowset was created.
     *
     * @return string
     */
    public function getTableClass()
    {
        return $this->_tableClass;
    }

    /**
     * Rewind the \Iterator to the first element.
     * Similar to the reset() function \for arrays in PHP.
     * Required by interface \Iterator.
     *
     * @return  RowsetAbstract  Fluent interface.
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    /**
     * Return the current element.
     * Similar to the current() function \for arrays in PHP
     * Required by interface \Iterator.
     *
     * @return \Zend\Db\Table\Row\RowAbstract current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        // do we already have a row object \for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'table'    => $this->_table,
                    'data'     => $this->_data[$this->_pointer],
                    'stored'   => $this->_stored,
                    'readOnly' => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
    }

    /**
     * Return the identifying key \of the current element.
     * Similar to the key() function \for arrays in PHP.
     * Required by interface \Iterator.
     *
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function \for arrays in PHP.
     * Required by interface \Iterator.
     *
     * @return void
     */
    public function next()
    {
        ++$this->_pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end \of the collection.
     * Required by interface \Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->_pointer < $this->_count;
    }

    /**
     * Returns the number \of elements in the collection.
     *
     * Implements \Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }
    
    /**
     * Take the \Iterator to position $position
     * Required by interface \SeekableIterator.
     *
     * @param int $position the position to seek to
     * @return  RowsetAbstract 
     * @throws  DbTableRowsetException 
     */
    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position > $this->_count) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new  DbTableRowsetException ("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;        
    }

    /**
     * Returns a \Zend\Db\Table\Row from a known position into the \Iterator
     *
     * @param int $position the position \of the row expected
     * @param bool $seek wether or not seek the iterator to that position after
     * @return \Zend\Db\Table\Row
     * @throws  DbTableRowsetException 
     */
    public function getRow($position, $seek = false)
    {
        $key = $this->key();
        try {
            $this->seek($position);
            $row = $this->current();
        } catch ( DbTableRowsetException  $e) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new  DbTableRowsetException ('No row could be found at position ' . (int) $position);
        }
        if ($seek == false) {
            $this->seek($key);
        }
        return $row;
    }

    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @return array
     */
    public function toArray()
    {
        // @todo This works only if we have iterated through
        // the result set once to instantiate the rows.
        foreach ($this->_rows as $i => $row) {
            $this->_data[$i] = $row->toArray();
        }
        return $this->_data;
    }

}
