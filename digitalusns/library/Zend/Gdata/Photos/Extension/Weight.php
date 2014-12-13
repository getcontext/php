<?php

namespace Zend\Gdata\Photos\Extension;



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
 * @see  Photos 
 */
require_once 'Zend/Gdata/Photos.php';

/**
 * Represents the gphoto:weight element used by the API.
 * This indicates the weight \of a tag, based on the number \of times
 * it appears in photos under the current element.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Extension as Extension;
use Zend\Gdata\Photos as Photos;




class  Weight  extends  Extension 
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'weight';
    
    /**
     * Constructs a new  Weight  object.
     * 
     * @param string $text (optional) The value to represent.
     */
    public function __construct($text = null) 
    {
        foreach ( Photos ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->setText($text);
    }

}
