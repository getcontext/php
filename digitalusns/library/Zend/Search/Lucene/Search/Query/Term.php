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

/**  SearchLuceneSearchWeightTerm  */
require_once 'Zend/Search/Lucene/Search/Weight/Term.php';


/**
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Search\Query\MultiTerm as MultiTerm;
use Zend\Search\Lucene\Search\Query\QueryEmpty as SearchLuceneSearchQueryEmpty;
use Zend\Search\Lucene\Search\Weight\Term as SearchLuceneSearchWeightTerm;
use Zend\Search\Lucene\Document\Html as Html;
use Zend\Search\Lucene\Search\Query as Query;
use Zend\Search\Lucene\Index\Term as SearchLuceneIndexTerm;
use Zend\Search\Lucene\LuceneInterface as SearchLuceneInterface;




class  Term  extends  Query 
{
    /**
     * Term to find.
     *
     * @var  SearchLuceneIndexTerm 
     */
    private $_term;

    /**
     * Documents vector.
     *
     * @var array
     */
    private $_docVector = null;

    /**
     * Term freqs vector.
     * array(docId => freq, ...)
     *
     * @var array
     */
    private $_termFreqs;


    /**
     *  Term  constructor
     *
     * @param  SearchLuceneIndexTerm  $term
     * @param boolean $sign
     */
    public function __construct( SearchLuceneIndexTerm  $term)
    {
        $this->_term = $term;
    }

    /**
     * Re-write query into primitive queries in the context \of specified index
     *
     * @param  SearchLuceneInterface  $index
     * @return  Query 
     */
    public function rewrite( SearchLuceneInterface  $index)
    {
        if ($this->_term->field != null) {
            return $this;
        } else {
            $query = new  MultiTerm ();
            $query->setBoost($this->getBoost());

            foreach ($index->getFieldNames(true) as $fieldName) {
                $term = new  SearchLuceneIndexTerm ($this->_term->text, $fieldName);

                $query->addTerm($term);
            }

            return $query->rewrite($index);
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
        // Check, that index contains specified term
        if (!$index->hasTerm($this->_term)) {
            return new  SearchLuceneSearchQueryEmpty ();
        }

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
        $this->_weight = new  SearchLuceneSearchWeightTerm ($this->_term, $this, $reader);
        return $this->_weight;
    }

    /**
     * Execute query in context \of index reader
     * It also initializes necessary internal structures
     *
     * @param  SearchLuceneInterface  $reader
     */
    public function execute( SearchLuceneInterface  $reader)
    {
        $this->_docVector = array_flip($reader->termDocs($this->_term));
        $this->_termFreqs = $reader->termFreqs($this->_term);

        // Initialize weight if it's not done yet
        $this->_initWeight($reader);
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
        return $this->_docVector;
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
        if (isset($this->_docVector[$docId])) {
            return $reader->getSimilarity()->tf($this->_termFreqs[$docId]) *
                   $this->_weight->getValue() *
                   $reader->norm($docId, $this->_term->field) *
                   $this->getBoost();
        } else {
            return 0;
        }
    }

    /**
     * Return query terms
     *
     * @return array
     */
    public function getQueryTerms()
    {
        return array($this->_term);
    }

    /**
     * Return query term
     *
     * @return  SearchLuceneIndexTerm 
     */
    public function getTerm()
    {
        return $this->_term;
    }

    /**
     * Returns query term
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->_terms;
    }

    /**
     * Highlight query terms
     *
     * @param integer &$colorIndex
     * @param  Html  $doc
     */
    public function highlightMatchesDOM( Html  $doc, &$colorIndex)
    {
        $doc->highlight($this->_term->text, $this->_getHighlightColor($colorIndex));
    }

    /**
     * Print a query
     *
     * @return string
     */
    public function __toString()
    {
        // It's used only \for query visualisation, so we don't care about characters escaping
        return (($this->_term->field === null)? '':$this->_term->field . ':') . $this->_term->text;
    }
}

