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
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Rowset.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * @see  DbTableRowsetAbstract 
 */
require_once 'Zend/Db/Table/Rowset/Abstract.php';


/**
 * \Reference concrete class that extends  DbTableRowsetAbstract .
 * Developers may also create their own classes that extend the abstract class.
 *
 * @category   Zend
 * @package    \Zend\Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Db\Table\Rowset\RowsetAbstract as DbTableRowsetAbstract;




class  Rowset  extends  DbTableRowsetAbstract 
{
}
