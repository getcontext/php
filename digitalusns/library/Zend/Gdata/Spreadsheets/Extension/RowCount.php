<?php

namespace Zend\Gdata\Spreadsheets\Extension;


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
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Entry.php 3941 2007-03-14 21:36:13Z darby $
 */

/**
 * @see \Zend\Gdata\Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see  Extension 
 */
require_once 'Zend/Gdata/Extension.php';


/**
 * Concrete class \for working with RowCount elements.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Spreadsheets as Spreadsheets;
use Zend\Gdata\Extension as Extension;




class  RowCount  extends  Extension 
{

    protected $_rootElement = 'rowCount';
    protected $_rootNamespace = 'gs';

    /**
     * Constructs a new  RowCount  object.
     * @param string $text (optional) The text content \of the element.
     */
    public function __construct($text = null)
    {
        foreach ( Spreadsheets ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->_text = $text;
    }

}
