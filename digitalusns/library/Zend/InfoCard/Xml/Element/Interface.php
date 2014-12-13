<?php

namespace Zend\InfoCard\Xml\Element;


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
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 * The Interface used to represent an XML \Data Type
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */






interface  ElementInterface 
{
    /**
     * Return the data within the object as an XML document
     */
    public function asXML();

    /**
     * Magic function which allows us to treat the object as a string to return XML
     * (same as the asXML() method)
     */
    public function __toString();
}
