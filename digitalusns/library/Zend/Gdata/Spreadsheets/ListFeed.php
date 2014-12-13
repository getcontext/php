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
 * @see  Feed 
 */
require_once 'Zend/Gdata/Feed.php';

/**
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Spreadsheets as Spreadsheets;
use Zend\Gdata\Feed as Feed;




class  ListFeed  extends  Feed 
{

    /**
     * The classname \for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\Spreadsheets\ListEntry';

    /**
     * The classname \for the feed.
     *
     * @var string
     */
    protected $_feedClassName = '\Zend\Gdata\Spreadsheets\ListFeed';

    /**
     * Constructs a new  ListFeed  object.
     * @param DOMElement $element An existing XML element on which to base this new object.
     */
    public function __construct($element = null)
    {
        foreach ( Spreadsheets ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

}
