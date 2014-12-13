<?php

namespace Zend\Paginator\Adapter;


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
 * @package    \Zend\Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DbSelect.php 10981 2008-08-22 21:01:20Z norm2782 $
 */

/**
 * @see  PaginatorAdapterInterface 
 */
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @see  Db 
 */
require_once 'Zend/Db.php';

/**
 * @see  Select 
 */
require_once 'Zend/Db/Select.php';

/**
 * @category   Zend
 * @package    \Zend\Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapterInterface;
use Zend\Paginator\Exception as PaginatorException;
use Zend\Db\Select as Select;
use Zend\Db\Expr as Expr;
use Zend\Db as Db;




class  DbSelect  implements  PaginatorAdapterInterface 
{
    /**
     * Name \of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * Database query
     *
     * @var  Select 
     */
    protected $_select = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    /**
     * Constructor.
     *
     * @param  Select  $select The select query
     */
    public function __construct( Select  $select)
    {
        $this->_select = $select;
    }

    /**
     * Sets the total row count, either directly or through a supplied
     * query.  Without setting this, {@link getPages()} selects the count
     * as a subquery (SELECT COUNT ... FROM (SELECT ...)).  While this 
     * yields an accurate count even with queries containing clauses like 
     * LIMIT, it can be slow in some circumstances.  For example, in MySQL, 
     * subqueries are generally slow when using the InnoDB storage engine.  
     * Users are therefore encouraged to profile their queries to find 
     * the solution that best meets their needs.
     *
     * @param   Select |integer $totalRowCount Total row count integer 
     *                                               or query
     * @return  DbSelect  $this
     * @throws  PaginatorException 
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof  Select ) {
            $columns = $rowCount->getPart( Select ::COLUMNS);
            
            if (false === strpos((string) $columns[0][1], self::ROW_COUNT_COLUMN)) {
                /**
                 * @see  PaginatorException 
                 */
                require_once 'Zend/Paginator/Exception.php';
                
                throw new  PaginatorException ('Row count column not found');
            }
            
            $result = $rowCount->query( Db ::FETCH_ASSOC)->fetch();
            
            $this->_rowCount = count($result) > 0 ? $result[self::ROW_COUNT_COLUMN] : 0;
        } else if (is_integer($rowCount)) {
            $this->_rowCount = $rowCount;
        } else {
            /**
             * @see  PaginatorException 
             */
            require_once 'Zend/Paginator/Exception.php';
            
            throw new  PaginatorException ('Invalid row count');
        }

        return $this;
    }

    /**
     * Returns an array \of items \for a page.
     *
     * @param  integer $offset \Page offset
     * @param  integer $itemCountPerPage Number \of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_select->limit($itemCountPerPage, $offset);
        
        return $this->_select->query()->fetchAll();
    }

    /**
     * Returns the total number \of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_rowCount === null) {
            $rowCount   = clone $this->_select;
            
            /**
             * The DISTINCT and GROUP BY queries only work when selecting one column.
             * The question is whether any RDBMS supports DISTINCT \for multiple columns, without workarounds.
             */
            if (true === $rowCount->getPart( Select ::DISTINCT)) {
                $columnParts = $rowCount->getPart( Select ::COLUMNS);
                
                $columns = array();
                
                foreach ($columnParts as $part) {
                	$columns[] = $part[1];
                }
                
                $groupPart = implode(',', $columns);
            } else {
                $groupPart = implode(',', $rowCount->getPart( Select ::GROUP));
            }
            
            $countPart  = empty($groupPart) ? 'COUNT(*)' : 'COUNT(DISTINCT ' . $groupPart . ')';
            $expression = new  Expr ($countPart . ' AS ' . self::ROW_COUNT_COLUMN);  
            
            $rowCount->__toString(); // Workaround \for ZF-3719 and related
            $rowCount->reset( Select ::COLUMNS)
                     ->reset( Select ::ORDER)
                     ->reset( Select ::LIMIT_OFFSET)
                     ->reset( Select ::GROUP)
                     ->reset( Select ::DISTINCT)
                     ->columns($expression);
            
            $this->setRowCount($rowCount);
        }

        return $this->_rowCount;
    }
}
