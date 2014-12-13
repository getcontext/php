<?php

namespace Zend\Dojo\Form\Decorator;


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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  DijitContainer  */
require_once 'Zend/Dojo/Form/Decorator/DijitContainer.php';

/**
 * AccordionContainer
 *
 * Render a dijit AccordionContainer
 *
 * @uses        DijitContainer 
 * @package    \Zend\Dojo
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AccordionContainer.php 10009 2008-07-09 16:52:18Z matthew $
 */


use Zend\Dojo\Form\Decorator\DijitContainer as DijitContainer;




class  AccordionContainer  extends  DijitContainer 
{
    /**
     * \View helper
     * @var string
     */
    protected $_helper = 'AccordionContainer';
}
