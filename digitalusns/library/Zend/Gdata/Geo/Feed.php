<?php

namespace Zend\Gdata\Geo;



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
 * @see \Zend\Gdata_eed
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * @see  Geo 
 */
require_once 'Zend/Gdata/Geo.php';

/**
 * @see  Geo _Entry
 */
require_once 'Zend/Gdata/Geo/Entry.php';

/**
 * Feed \for Gdata Geographic data entries.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Feed as GdataFeed;
use Zend\Gdata\Geo as Geo;




class  Feed  extends  GdataFeed 
{

    /**
     * The classname \for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\Geo\Entry';

    public function __construct($element = null)
    {
        foreach ( Geo ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

}
