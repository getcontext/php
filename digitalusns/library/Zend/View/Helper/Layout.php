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
 * @version    $Id: Layout.php 10665 2008-08-05 10:57:18Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  ViewHelperAbstract .php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * \View helper \for retrieving layout object
 *
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\Helper\HelperAbstract as ViewHelperAbstract;
use Zend\Layout as ZendLayout;




class  Layout  extends  ViewHelperAbstract 
{
    /** @var  ZendLayout  */
    protected $_layout;

    /**
     * Get layout object
     *
     * @return  ZendLayout 
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            require_once 'Zend/Layout.php';
            $this->_layout =  ZendLayout ::getMvcInstance();
            if (null === $this->_layout) {
                // Implicitly creates layout object
                $this->_layout = new  ZendLayout ();
            }
        }

        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param   ZendLayout  $layout
     * @return  ZendLayout _Controller_Action_Helper_Layout
     */
    public function setLayout( ZendLayout  $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Return layout object
     *
     * Usage: $this->layout()->setLayout('alternate');
     *
     * @return  ZendLayout 
     */
    public function layout()
    {
        return $this->getLayout();
    }
}
