<?php

namespace Zend\Gdata\Exif\Extension;



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
 * @see  Extension 
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * @see  Exif 
 */
require_once 'Zend/Gdata/Exif.php';

/**
 * Represents the exif:distance element used by the Gdata Exif extensions.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Extension as Extension;
use Zend\Gdata\Exif as Exif;




class  Distance  extends  Extension 
{

    protected $_rootNamespace = 'exif';
    protected $_rootElement = 'distance';
    
    /**
     * Constructs a new  Distance  object.
     * 
     * @param string $text (optional) The value to use \for this element.
     */
    public function __construct($text = null) 
    {
        foreach ( Exif ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->setText($text);
    }

}
