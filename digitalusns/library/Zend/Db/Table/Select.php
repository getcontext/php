<?php

namespace Zend\Db\Table;



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
 * @subpackage Select
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Select.php 5308 2007-06-14 17:18:45Z bkarwin $
 */


/**
 * @see  DbSelect 
 */
require_once 'Zend/Db/Select.php';


/**
 * @see  DbTableAbstract 
 */
require_once 'Zend/Db/Table/Abstract.php';


/**
 * Class \for SQL SELECT query manipulation \for the \\Zend\Db_Table component.
 *
 * @category   Zend
 * @package    \Zend\Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Db\Table\Select\Exception as DbTableSelectException;
use Zend\Db\Table\TableAbstract as DbTableAbstract;
use Zend\Db\Select as DbSelect;
use Zend\Db\Expr as Expr;




class  Select  extends  DbSelect 
{
    /**
     * Table schema \for parent \\Zend\Db_Table.
     *
     * @var array
     */
    protected $_info;

    /**
     * Table integrity override.
     *
     * @var array
     */
    protected $_integrityCheck = true;

    /**
     * Class constructor
     *
     * @param  DbTableAbstract  $adapter
     */
    public function __construct( DbTableAbstract  $table)
    {
        parent::__construct($table->getAdapter());
        $this->setTable($table);
    }

    /**
     * Sets the primary table name and retrieves the table schema.
     *
     * @param  DbTableAbstract  $adapter
     * @return  DbSelect  This  DbSelect  object.
     */
    public function setTable( DbTableAbstract  $table)
    {
        $this->_adapter = $table->getAdapter();
        $this->_info    = $table->info();
        return $this;
    }

    /**
     * Sets the integrity check flag.
     *
     * Setting this flag to false skips the checks \for table joins, allowing
     * 'hybrid' table rows to be created.
     *
     * @param  DbTableAbstract  $adapter
     * @return  DbSelect  This  DbSelect  object.
     */
    public function setIntegrityCheck($flag = true)
    {
        $this->_integrityCheck = $flag;
        return $this;
    }

    /**
     * Tests query to determine if expressions or aliases columns exist.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        $readOnly = false;
        $fields   = $this->getPart( Select ::COLUMNS);
        $cols     = $this->_info[ DbTableAbstract ::COLS];

        if (!count($fields)) {
            return $readOnly;
        }

        foreach ($fields as $columnEntry) {
            $column = $columnEntry[1];
            $alias = $columnEntry[2];

            if ($alias !== null) {
                $column = $alias;
            }
            
            switch (true) {
                case ($column == self::SQL_WILDCARD):
                    break;

                case ($column instanceof  Expr ):
                case (!in_array($column, $cols)):
                    $readOnly = true;
                    break 2;
            }
        }

        return $readOnly;
    }

    /**
     * Adds a FROM table and optional columns to the query.
     *
     * The table name can be expressed
     *
     * @param  array|string| Expr | DbTableAbstract  $name The table name or an 
                                                                      associative array relating 
                                                                      table name to correlation
                                                                      name.
     * @param  array|string| Expr  $cols The columns to select from this table.
     * @param  string $schema The schema name to specify, if any.
     * @return  Select  This  Select  object.
     */
    public function from($name, $cols = self::SQL_WILDCARD, $schema = null)
    {
        if ($name instanceof  DbTableAbstract ) {
            $info = $name->info();
            $name = $info[ DbTableAbstract ::NAME];
            if (isset($info[ DbTableAbstract ::SCHEMA])) {
                $schema = $info[ DbTableAbstract ::SCHEMA];
            }
        }

        return $this->joinInner($name, null, $cols, $schema);
    }

    /**
     * Performs a validation on the select query before passing back to the parent class.
     * Ensures that only columns from the primary \\Zend\Db_Table are returned in the result.
     *
     * @return string This object as a SELECT string.
     */
    public function assemble()
    {
        $fields  = $this->getPart( Select ::COLUMNS);
        $primary = $this->_info[ DbTableAbstract ::NAME];
        $schema  = $this->_info[ DbTableAbstract ::SCHEMA];

        // If no fields are specified we assume all fields from primary table
        if (!count($fields)) {
            $this->from($primary, self::SQL_WILDCARD, $schema);
            $fields = $this->getPart( Select ::COLUMNS);
        }

        $from = $this->getPart( Select ::FROM);

        if ($this->_integrityCheck !== false) {
            foreach ($fields as $columnEntry) {
                list($table, $column) = $columnEntry;
                
                // Check each column to ensure it only references the primary table
                if ($column) {
                    if (!isset($from[$table]) || $from[$table]['tableName'] != $primary) {
                        require_once 'Zend/Db/Table/Select/Exception.php';
                        throw new  DbTableSelectException ('Select query cannot join with another table');
                    }
                }
            }
        }

        return parent::assemble();
    }
}
