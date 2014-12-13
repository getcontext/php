<?php

namespace Zend\Service\Technorati;


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
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: GetInfoResult.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * Represents a single Technorati GetInfo query result object.
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Technorati\Author as Author;
use Zend\Service\Technorati\Weblog as Weblog;




class  GetInfoResult 
{
    /**
     * Technorati author
     *
     * @var      Author 
     * @access  protected
     */
    protected $_author;

    /**
     * A list \of weblogs claimed by this author
     *
     * @var     array
     * @access  protected
     */
    protected $_weblogs = array();


    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomDocument $dom the ReST fragment \for this object
     */
    public function __construct(DomDocument $dom)
    {
        $xpath = new DOMXPath($dom);

        /**
         * @see  Author 
         */
        require_once 'Zend/Service/Technorati/Author.php';

        $result = $xpath->query('//result');
        if ($result->length == 1) {
            $this->_author = new  Author ($result->item(0));
        }

        /**
         * @see  Weblog 
         */
        require_once 'Zend/Service/Technorati/Weblog.php';

        $result = $xpath->query('//item/weblog');
        if ($result->length >= 1) {
            foreach ($result as $weblog) {
                $this->_weblogs[] = new  Weblog ($weblog);
            }
        }
    }


    /**
     * Returns the author associated with queried username.
     * 
     * @return   Author 
     */
    public function getAuthor() {
        return $this->_author;
    }

    /**
     * Returns the collection \of weblogs authored by queried username.
     * 
     * @return  array \of  Weblog 
     */
    public function getWeblogs() {
        return $this->_weblogs;
    }

}
