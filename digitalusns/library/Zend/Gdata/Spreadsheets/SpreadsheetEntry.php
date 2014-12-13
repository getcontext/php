<?php

namespace Zend\Gdata\Spreadsheets;



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
 */

/**
 * @see  Entry 
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * Concrete class \for working with Atom entries.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Spreadsheets as Spreadsheets;
use Zend\Gdata\Entry as Entry;




class  SpreadsheetEntry  extends  Entry 
{

    protected $_entryClassName = '\Zend\Gdata\Spreadsheets\SpreadsheetEntry';

    /**
     * Constructs a new  SpreadsheetEntry  object.
     * @param DOMElement $element (optional) The DOMElement on which to base this object.
     */
    public function __construct($element = null)
    {
        foreach ( Spreadsheets ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

    /**
     * Returns the worksheets in this spreadsheet
     *
     * @return  Spreadsheets _WorksheetFeed The worksheets 
     */
    public function getWorksheets()
    {
        $service = new  Spreadsheets ($this->getHttpClient());
        return $service->getWorksheetFeed($this);
    }

}
