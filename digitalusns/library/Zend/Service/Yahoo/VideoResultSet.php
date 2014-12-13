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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: VideoResultSet.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * @see  ResultSet 
 */
require_once 'Zend/Service/Yahoo/ResultSet.php';


/**
 * @see  VideoResult 
 */
require_once 'Zend/Service/Yahoo/VideoResult.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Yahoo\VideoResult as VideoResult;
use Zend\Service\Yahoo\ResultSet as ResultSet;




class  VideoResultSet  extends  ResultSet 
{
    /**
     * Video result set namespace
     *
     * @var string
     */
    protected $_namespace = 'urn:yahoo:srchmv';


    /**
     * Overrides  ResultSet ::current()
     *
     * @return  VideoResult 
     */
    public function current()
    {
        return new  VideoResult ($this->_results->item($this->_currentIndex));
    }
}
