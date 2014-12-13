<?php

namespace Zend\Search\Lucene\Analysis\Analyzer;


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
 * @package    \Zend\Search\Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Analyzer  */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';


/**
 * Common implementation \of the  Analyzer  interface.
 * There are several standard standard subclasses provided by \Zend\Search\Lucene/Analysis
 * subpackage:  Common _Text, ZSearchHTMLAnalyzer, ZSearchXMLAnalyzer.
 *
 * @todo ZSearchHTMLAnalyzer and ZSearchXMLAnalyzer implementation
 *
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Search\Lucene\Analysis\TokenFilter as TokenFilter;
use Zend\Search\Lucene\Analysis\Analyzer as Analyzer;
use Zend\Search\Lucene\Analysis\Token as Token;



abstract class  Common  extends  Analyzer 
{
    /**
     * The set \of Token filters applied to the Token stream.
     * Array \of  TokenFilter  objects.
     *
     * @var array
     */
    private $_filters = array();

    /**
     * Add Token filter to the Analyzer
     *
     * @param  TokenFilter  $filter
     */
    public function addFilter( TokenFilter  $filter)
    {
        $this->_filters[] = $filter;
    }

    /**
     * Apply filters to the token. Can return null when the token was removed.
     *
     * @param  Token  $token
     * @return  Token 
     */
    public function normalize( Token  $token)
    {
        foreach ($this->_filters as $filter) {
            $token = $filter->normalize($token);

            // resulting token can be null if the filter removes it
            if (is_null($token)) {
                return null;
            }
        }

        return $token;
    }
}

