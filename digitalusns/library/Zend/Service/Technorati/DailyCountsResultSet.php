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
 * @version    $Id: DailyCountsResultSet.php 8064 2008-02-16 10:58:39Z thomas $
 */


/** 
 * @see \Zend\Date
 */
require_once 'Zend/Date.php';

/** 
 * @see  ResultSet  
 */
require_once 'Zend/Service/Technorati/ResultSet.php';

/**
 * @see  Utils 
 */
require_once 'Zend/Service/Technorati/Utils.php';


/**
 * Represents a Technorati Tag query result set.
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Technorati\DailyCountsResult as DailyCountsResult;
use Zend\Service\Technorati\ResultSet as ResultSet;
use Zend\Service\Technorati\Utils as Utils;
use Zend\Locale as Locale;




class  DailyCountsResultSet  extends  ResultSet 
{
    /**
     * Technorati search URL \for given query.
     *
     * @var     \Zend\Uri\Http
     * @access  protected
     */
    protected $_searchUrl;

    /**
     * Number \of days \for which counts provided.
     * 
     * @var     \Zend\Service\Technorati\Weblog
     * @access  protected
     */
    protected $_days;

    /**
     * Parses the search response and retrieve the results \for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment \for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);
        
        // default locale prevent \Zend\Date to fail
        // when script is executed via shell
        //  Locale ::setDefault('en');

        $result = $this->_xpath->query('/tapi/document/result/days/text()');
        if ($result->length == 1) $this->_days = (int) $result->item(0)->data;

        $result = $this->_xpath->query('/tapi/document/result/searchurl/text()');
        if ($result->length == 1) {
            $this->_searchUrl =  Utils ::normalizeUriHttp($result->item(0)->data);
        }

        $this->_totalResultsReturned  = (int) $this->_xpath->evaluate("count(/tapi/document/items/item)");
        $this->_totalResultsAvailable = (int) $this->getDays();
    }


    /**
     * Returns the search URL \for given query.
     * 
     * @return  \Zend\Uri\Http
     */
    public function getSearchUrl() {
        return $this->_searchUrl;
    }

    /**
     * Returns the number \of days \for which counts provided.
     * 
     * @return  int
     */
    public function getDays() {
        return $this->_days;
    }

    /**
     * Implements  ResultSet ::current().
     *
     * @return  DailyCountsResult  current result
     */
    public function current()
    {
        /**
         * @see  DailyCountsResult 
         */
        require_once 'Zend/Service/Technorati/DailyCountsResult.php';
        return new  DailyCountsResult ($this->_results->item($this->_currentIndex));
    }
}
