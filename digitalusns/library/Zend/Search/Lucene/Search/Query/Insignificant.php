<?php

namespace Zend\Search\Lucene\Search\Query;


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
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Query  */
require_once 'Zend/Search/Lucene/Search/Query.php';

/**  SearchLuceneSearchWeightEmpty  */
require_once 'Zend/Search/Lucene/Search/Weight/Empty.php';


/**
 * The insignificant query returns empty result, but doesn't limit result set as a part \of other queries
 *
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Search\Weight\WeightEmpty as SearchLuceneSearchWeightEmpty;
use Zend\Search\Lucene\Document\Html as Html;
use Zend\Search\Lucene\Search\Query as Query;
use Zend\Search\Lucene\LuceneInterface as SearchLuceneInterface;




class  Insignificant  extends  Query 
{
    /**
     * Re-write query into primitive queries in the context \of specified index
     *
     * @param  SearchLuceneInterface  $index
     * @return  Query 
     */
    public function rewrite( SearchLuceneInterface  $index)
    {
        return $this;
    }

    /**
     * Optimize query in the context \of specified index
     *
     * @param  SearchLuceneInterface  $index
     * @return  Query 
     */
    public function optimize( SearchLuceneInterface  $index)
    {
        return $this;
    }

    /**
     * Constructs an appropriate Weight implementation \for this query.
     *
     * @param  SearchLuceneInterface  $reader
     * @return \Zend\Search\Lucene\Search\Weight
     */
    public function createWeight( SearchLuceneInterface  $reader)
    {
        return new  SearchLuceneSearchWeightEmpty ();
    }

    /**
     * Execute query in context \of index reader
     * It also initializes necessary internal structures
     *
     * @param  SearchLuceneInterface  $reader
     */
    public function execute( SearchLuceneInterface  $reader)
    {
        // Do nothing
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     */
    public function matchedDocs()
    {
        return array();
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param  SearchLuceneInterface  $reader
     * @return float
     */
    public function score($docId,  SearchLuceneInterface  $reader)
    {
        return 0;
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        return array();
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param  Html  $doc
     */
    public function highlightMatchesDOM( Html  $doc, &$colorIndex)
    {
        // Do nothing
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        return '<InsignificantQuery>';
    }
}

