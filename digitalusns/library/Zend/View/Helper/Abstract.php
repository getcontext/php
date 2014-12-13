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
 * @category   Zend
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 10665 2008-08-05 10:57:18Z matthew $
 */

/**
 * @see  ViewHelperInterface 
 */
require_once 'Zend/View/Helper/Interface.php';

/**
 * @category   Zend
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\View\Helper\HelperInterface as ViewHelperInterface;
use Zend\View\ViewInterface as ViewInterface;



abstract class  HelperAbstract  implements  ViewHelperInterface 
{
    /**
     * \View object
     *
     * @var  ViewInterface 
     */
    public $view = null;
    
    /**
     * Set the \View object
     *
     * @param   ViewInterface  $view
     * @return  HelperAbstract 
     */
    public function setView( ViewInterface  $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Strategy pattern: currently unutilized
     *
     * @return void
     */
    public function direct()
    {
    }
}
