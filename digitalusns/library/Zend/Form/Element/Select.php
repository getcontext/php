<?php

namespace Zend\Form\Element;


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
 * @package    \Zend\Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Multi  */
require_once 'Zend/Form/Element/Multi.php';

/**
 * Select.php form element
 * 
 * @category   Zend
 * @package    \Zend\Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Select.php 8064 2008-02-16 10:58:39Z thomas $
 */


use Zend\Form\Element\Multi as Multi;




class  Select  extends  Multi 
{
    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formSelect';
}
