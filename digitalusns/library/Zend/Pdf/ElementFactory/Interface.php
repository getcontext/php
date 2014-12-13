<?php

namespace Zend\Pdf\ElementFactory;


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
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PDF element factory interface.
 * Responsibility is to log PDF changes
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Object\Stream as Stream;
use Zend\Pdf\Element\Object as Object;
use Zend\Pdf\ElementFactory as ElementFactory;
use Zend\Pdf\Element as Element;




interface  ElementFactoryInterface 
{
    /**
     * Close factory and clean-up resources
     *
     * @internal
     */
    public function close();

    /**
     * Get source factory object
     *
     * @return  ElementFactory 
     */
    public function resolve();

    /**
     * Get factory ID
     *
     * @return integer
     */
    public function getId();

    /**
     * Set object counter
     *
     * @param integer $objCount
     */
    public function setObjectCount($objCount);

    /**
     * Get object counter
     *
     * @return integer
     */
    public function getObjectCount();

    /**
     * Attach factory to the current;
     *
     * @param  ElementFactoryInterface  $factory
     */
    public function attach( ElementFactoryInterface  $factory);

    /**
     * Calculate object enumeration shift.
     *
     * @internal
     * @param  ElementFactoryInterface  $factory
     * @return integer
     */
    public function calculateShift( ElementFactoryInterface  $factory);

    /**
     * Retrive object enumeration shift.
     *
     * @param  ElementFactoryInterface  $factory
     * @return integer
     * @throws \Zend\Pdf\Exception
     */
    public function getEnumerationShift( ElementFactoryInterface  $factory);

    /**
     * Mark object as modified in context \of current factory.
     *
     * @param  Object  $obj
     * @throws \Zend\Pdf\Exception
     */
    public function markAsModified( Object  $obj);

    /**
     * Remove object in context \of current factory.
     *
     * @param  Object  $obj
     * @throws \Zend\Pdf\Exception
     */
    public function remove( Object  $obj);

    /**
     * Generate new  Object 
     *
     * @todo Reusage \of the freed object. It's not a support \of new feature, but only improvement.
     *
     * @param  Element  $objectValue
     * @return  Object 
     */
    public function newObject( Element  $objectValue);

    /**
     * Generate new  Stream 
     *
     * @todo Reusage \of the freed object. It's not a support \of new feature, but only improvement.
     *
     * @param mixed $objectValue
     * @return  Stream 
     */
    public function newStreamObject($streamValue);

    /**
     * Enumerate modified objects.
     * Returns array \of \Zend\Pdf\UpdateInfoContainer
     *
     * @param  ElementFactory  $rootFactory
     * @return array
     */
    public function listModifiedObjects($rootFactory = null);

    /**
     * Check if PDF file was modified
     *
     * @return boolean
     */
    public function isModified();
}

