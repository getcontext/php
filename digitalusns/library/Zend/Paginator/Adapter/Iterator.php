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
 * @version    $Id: \Iterator.php 10013 2008-07-09 21:08:06Z norm2782 $
 */

/**
 * @see  PaginatorAdapterInterface 
 */
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @category   Zend
 * @package    \Zend\Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapterInterface;
use Zend\Paginator\Exception as PaginatorException;




class  Iterator  implements  PaginatorAdapterInterface 
{
    /**
     * \Iterator which implements \Countable
     * 
     * @var \Iterator
     */
    protected $_iterator = null;
    
    /**
     * Item count
     *
     * @var integer
     */
    protected $_count = null;

    /**
     * Constructor.
     * 
     * @param  \Iterator $iterator \Iterator to paginate
     * @throws  PaginatorException 
     */
    public function __construct(\Iterator $iterator)
    {
        if (!$iterator instanceof \Countable) {
            /**
             * @see  PaginatorException 
             */
            require_once 'Zend/Paginator/Exception.php';
            
            throw new  PaginatorException ('\Iterator must implement \Countable');
        }

        $this->_iterator = $iterator;
        $this->_count = count($iterator);
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
        return new \LimitIterator($this->_iterator, $offset, $itemCountPerPage);
    }

    /**
     * Returns the total number \of rows in the collection.
     *
     * @return integer
     */
    public function count()
    {
        return $this->_count;
    }
}
