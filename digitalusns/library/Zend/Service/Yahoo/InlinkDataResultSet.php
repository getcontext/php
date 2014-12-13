<?php

namespace Zend\Service\Yahoo;


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
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


/**
 * @see  ResultSet 
 */
require_once 'Zend/Service/Yahoo/ResultSet.php';


/**
 * @see \Zend\Service\Yahoo\WebResult
 */
require_once 'Zend/Service/Yahoo/InlinkDataResult.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Yahoo\InlinkDataResult as InlinkDataResult;
use Zend\Service\Yahoo\ResultSet as ResultSet;




class  InlinkDataResultSet  extends  ResultSet 
{
    /**
     * Web result set namespace
     *
     * @var string
     */
    protected $_namespace = 'urn:yahoo:srch';


    /**
     * Overrides  ResultSet ::current()
     *
     * @return  InlinkDataResult 
     */
    public function current()
    {
        return new  InlinkDataResult ($this->_results->item($this->_currentIndex));
    }
}
