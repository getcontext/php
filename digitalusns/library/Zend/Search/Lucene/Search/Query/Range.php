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

/**  MultiTerm  */
require_once 'Zend/Search/Lucene/Search/Query/MultiTerm.php';


/**
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Search\Query\MultiTerm as MultiTerm;
use Zend\Search\Lucene\Search\Query\QueryEmpty as SearchLuceneSearchQueryEmpty;
use Zend\Search\Lucene\Search\Query\Term as SearchLuceneSearchQueryTerm;
use Zend\Search\Lucene\Document\Html as Html;
use Zend\Search\Lucene\Search\Query as Query;
use Zend\Search\Lucene\Index\Term as SearchLuceneIndexTerm;
use Zend\Search\Lucene\LuceneInterface as SearchLuceneInterface;
use Zend\Search\Lucene\Exception as SearchLuceneException;




class  Range  extends  Query 
{
    /**
     * Lower term.
     *
     * @var  SearchLuceneIndexTerm 
     */
    private $_lowerTerm;

    /**
     * Upper term.
     *
     * @var  SearchLuceneIndexTerm 
     */
    private $_upperTerm;


    /**
     * Search field
     *
     * @var string
     */
    private $_field;

    /**
     * Inclusive
     *
     * @var boolean
     */
    private $_inclusive;

    /**
     * Matched terms.
     *
     * Matched terms list.
     * It's filled during the search (rewrite operation) and may be used \for search result
     * post-processing
     *
     * Array \of  SearchLuceneIndexTerm  objects
     *
     * @var array
     */
    private $_matches;


    /**
     *  Range  constructor.
     *
     * @param  SearchLuceneIndexTerm |null $lowerTerm
     * @param  SearchLuceneIndexTerm |null $upperTerm
     * @param boolean $inclusive
     * @throws  SearchLuceneException 
     */
    public function __construct($lowerTerm, $upperTerm, $inclusive)
    {
        if ($lowerTerm === null  &&  $upperTerm === null) {
            throw new  SearchLuceneException ('At least one term must be non-null');
        }
        if ($lowerTerm !== null  &&  $upperTerm !== null  &&  $lowerTerm->field != $upperTerm->field) {
            throw new  SearchLuceneException ('Both terms must be \for the same field');
        }

        $this->_field     = ($lowerTerm !== null)? $lowerTerm->field : $upperTerm->field;
        $this->_lowerTerm = $lowerTerm;
        $this->_upperTerm = $upperTerm;
        $this->_inclusive = $inclusive;
    }

    /**
     * Get query field name
     *
     * @return string|null
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Get lower term
     *
     * @return  SearchLuceneIndexTerm |null
     */
    public function getLowerTerm()
    {
        return $this->_lowerTerm;
    }

    /**
     * Get upper term
     *
     * @return  SearchLuceneIndexTerm |null
     */
    public function getUpperTerm()
    {
        return $this->_upperTerm;
    }

    /**
     * Get upper term
     *
     * @return boolean
     */
    public function isInclusive()
    {
        return $this->_inclusive;
    }

    /**
     * Re-write query into primitive queries in the context \of specified index
     *
     * @param  SearchLuceneInterface  $index
     * @return  Query 
     */
    public function rewrite( SearchLuceneInterface  $index)
    {
        $this->_matches = array();

        if ($this->_field === null) {
            // Search through all fields
            $fields = $index->getFieldNames(true /* indexed fields list */);
        } else {
            $fields = array($this->_field);
        }

        foreach ($fields as $field) {
            $index->resetTermsStream();

            if ($this->_lowerTerm !== null) {
                $lowerTerm = new  SearchLuceneIndexTerm ($this->_lowerTerm->text, $field);

                $index->skipTo($lowerTerm);

                if (!$this->_inclusive  &&
                    $index->currentTerm() == $lowerTerm) {
                    // Skip lower term
                    $index->nextTerm();
                }
            } else {
                $index->skipTo(new  SearchLuceneIndexTerm ('', $field));
            }


            if ($this->_upperTerm !== null) {
                // Walk up to the upper term
                $upperTerm = new  SearchLuceneIndexTerm ($this->_upperTerm->text, $field);

                while ($index->currentTerm() !== null          &&
                       $index->currentTerm()->field == $field  &&
                       $index->currentTerm()->text  <  $upperTerm->text) {
                    $this->_matches[] = $index->currentTerm();
                    $index->nextTerm();
                }

                if ($this->_inclusive  &&  $index->currentTerm() == $upperTerm) {
                    // Include upper term into result
                    $this->_matches[] = $upperTerm;
                }
            } else {
                // Walk up to the end \of field data
                while ($index->currentTerm() !== null  &&  $index->currentTerm()->field == $field) {
                    $this->_matches[] = $index->currentTerm();
                    $index->nextTerm();
                }
            }

            $index->closeTermsStream();
        }

        if (count($this->_matches) == 0) {
            return new  SearchLuceneSearchQueryEmpty ();
        } else if (count($this->_matches) == 1) {
            return new  SearchLuceneSearchQueryTerm (reset($this->_matches));
        } else {
            $rewrittenQuery = new  MultiTerm ();

            foreach ($this->_matches as $matchedTerm) {
                $rewrittenQuery->addTerm($matchedTerm);
            }

            return $rewrittenQuery;
        }
    }

    /**
     * Optimize query in the context \of specified index
     *
     * @param  SearchLuceneInterface  $index
     * @return  Query 
     */
    public function optimize( SearchLuceneInterface  $index)
    {
        throw new  SearchLuceneException ('Range query should not be directly used \for search. Use $query->rewrite($index)');
    }

    /**
     * Return query terms
     *
     * @return array
     * @throws  SearchLuceneException 
     */
    public function getQueryTerms()
    {
        if ($this->_matches === null) {
            throw new  SearchLuceneException ('Search has to be performed first to get matched terms');
        }

        return $this->_matches;
    }

    /**
     * Constructs an appropriate Weight implementation \for this query.
     *
     * @param  SearchLuceneInterface  $reader
     * @return \Zend\Search\Lucene\Search\Weight
     * @throws  SearchLuceneException 
     */
    public function createWeight( SearchLuceneInterface  $reader)
    {
        throw new  SearchLuceneException ('Range query should not be directly used \for search. Use $query->rewrite($index)');
    }


    /**
     * Execute query in context \of index reader
     * It also initializes necessary internal structures
     *
     * @param  SearchLuceneInterface  $reader
     * @throws  SearchLuceneException 
     */
    public function execute( SearchLuceneInterface  $reader)
    {
        throw new  SearchLuceneException ('Range query should not be directly used \for search. Use $query->rewrite($index)');
    }

    /**
     * Get document ids likely matching the query
     *
     * It's an array with document ids as keys (performance considerations)
     *
     * @return array
     * @throws  SearchLuceneException 
     */
    public function matchedDocs()
    {
        throw new  SearchLuceneException ('Range query should not be directly used \for search. Use $query->rewrite($index)');
    }

    /**
     * Score specified document
     *
     * @param integer $docId
     * @param  SearchLuceneInterface  $reader
     * @return float
     * @throws  SearchLuceneException 
     */
    public function score($docId,  SearchLuceneInterface  $reader)
    {
        throw new  SearchLuceneException ('Range query should not be directly used \for search. Use $query->rewrite($index)');
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param  Html  $doc
     */
    public function highlightMatchesDOM( Html  $doc, &$colorIndex)
    {
        $words = array();

        foreach ($this->_matches as $term) {
            $words[] = $term->text;
        }

        $doc->highlight($words, $this->_getHighlightColor($colorIndex));
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only \for query visualisation, so we don't care about characters escaping
        return (($this->_field === null)? '' : $this->_field . ':')
             . (($this->_inclusive)? '[' : '{')
             . (($this->_lowerTerm !== null)?  $this->_lowerTerm->text : 'null')
             . ' TO '
             . (($this->_upperTerm !== null)?  $this->_upperTerm->text : 'null')
             . (($this->_inclusive)? ']' : '}');
    }
}

