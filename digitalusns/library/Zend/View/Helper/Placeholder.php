<?php

namespace Zend\View\Helper;


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
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Placeholder.php 10665 2008-08-05 10:57:18Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Registry  */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/**  ViewHelperAbstract .php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper \for passing data between otherwise segregated Views. It's called
 * Placeholder to make its typical usage obvious, but can be used just as easily
 * \for non-Placeholder things. That said, the support \for this is only
 * guaranteed to effect subsequently rendered templates, and \of course Layouts.
 *
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\Helper\Placeholder\Registry as Registry;
use Zend\View\Helper\HelperAbstract as ViewHelperAbstract;



 
class  Placeholder  extends  ViewHelperAbstract 
{  
    /**
     * Placeholder items
     * @var array
     */  
    protected $_items = array();  

    /**
     * @var  Registry 
     */
    protected $_registry;

    /**
     * Constructor
     *
     * Retrieve container registry from \Zend\Registry, or create new one and register it.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_registry =  Registry ::getRegistry();
    }
  
 
    /**
     * Placeholder helper
     * 
     * @param  string $name 
     * @return  Placeholder _Container_Abstract
     */  
    public function placeholder($name)  
    {  
        $name = (string) $name;  
        return $this->_registry->getContainer($name);
    }  

    /**
     * Retrieve the registry
     * 
     * @return  Registry 
     */
    public function getRegistry()
    {
        return $this->_registry;
    }
}
