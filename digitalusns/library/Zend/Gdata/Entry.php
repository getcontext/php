<?php

namespace Zend\Gdata;



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
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  MediaEntry 
 */
require_once 'Zend/Gdata/App/MediaEntry.php';

/**
 * Represents the GData flavor \of an Atom entry
 *
 * @category   Zend
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\MediaEntry as MediaEntry;
use Zend\Gdata as Gdata;




class  Entry  extends  MediaEntry 
{

    protected $_entryClassName = '\Zend\Gdata\Entry';

    public function __construct($element = null)
    {
        foreach ( Gdata ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

}
