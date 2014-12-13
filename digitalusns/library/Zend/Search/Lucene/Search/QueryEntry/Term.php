<?php

namespace Zend\Search\Lucene\Search\QueryEntry;


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


/**  SearchLuceneIndexTerm  */
require_once 'Zend/Search/Lucene/Index/Term.php';

/** \Zend\Search\Lucene\Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/**  QueryEntry  */
require_once 'Zend/Search/Lucene/Search/QueryEntry.php';

/**  QueryParserException  */
require_once 'Zend/Search/Lucene/Search/QueryParserException.php';

/**  Analyzer  */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';



/**
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Search\QueryParserException as QueryParserException;
use Zend\Search\Lucene\Search\Query\Insignificant as Insignificant;
use Zend\Search\Lucene\Search\Query\MultiTerm as MultiTerm;
use Zend\Search\Lucene\Search\Query\Wildcard as Wildcard;
use Zend\Search\Lucene\Search\Query\Fuzzy as Fuzzy;
use Zend\Search\Lucene\Search\QueryEntry as QueryEntry;
use Zend\Search\Lucene\Analysis\Analyzer as Analyzer;
use Zend\Search\Lucene\Search\Query\Term as SearchLuceneSearchQueryTerm;
use Zend\Search\Lucene\Index\Term as SearchLuceneIndexTerm;




class  Term  extends  QueryEntry 
{
    /**
     * Term value
     *
     * @var string
     */
    private $_term;

    /**
     * Field
     *
     * @var string|null
     */
    private $_field;


    /**
     * Fuzzy search query
     *
     * @var boolean
     */
    private $_fuzzyQuery = false;

    /**
     * Similarity
     *
     * @var float
     */
    private $_similarity = 1.;


    /**
     * Object constractor
     *
     * @param string $term
     * @param string $field
     */
    public function __construct($term, $field)
    {
        $this->_term  = $term;
        $this->_field = $field;
    }

    /**
     * Process modifier ('~')
     *
     * @param mixed $parameter
     */
    public function processFuzzyProximityModifier($parameter = null)
    {
        $this->_fuzzyQuery = true;

        if ($parameter !== null) {
            $this->_similarity = $parameter;
        } else {
            $this->_similarity =  Fuzzy ::DEFAULT_MIN_SIMILARITY;
        }
    }

    /**
     * Transform entry to a subquery
     *
     * @param string $encoding
     * @return \Zend\Search\Lucene\Search\Query
     * @throws  QueryParserException 
     */
    public function getQuery($encoding)
    {
        if (strpos($this->_term, '?') !== false || strpos($this->_term, '*') !== false) {
	        if ($this->_fuzzyQuery) {
	            throw new  QueryParserException ('Fuzzy search is not supported \for terms with wildcards.');
	        }

        	$pattern = '';

            $subPatterns = explode('*', $this->_term);

            $astericFirstPass = true;
            foreach ($subPatterns as $subPattern) {
                if (!$astericFirstPass) {
                    $pattern .= '*';
                } else {
                    $astericFirstPass = false;
                }

                $subPatternsL2 = explode('?', $subPattern);

                $qMarkFirstPass = true;
                foreach ($subPatternsL2 as $subPatternL2) {
                    if (!$qMarkFirstPass) {
                        $pattern .= '?';
                    } else {
                        $qMarkFirstPass = false;
                    }

                    $tokens =  Analyzer ::getDefault()->tokenize($subPatternL2, $encoding);
                    if (count($tokens) > 1) {
                        throw new  QueryParserException ('Wildcard search is supported only \for non-multiple word terms');
                    }

                    foreach ($tokens as $token) {
                        $pattern .= $token->getTermText();
                    }
                }
            }

            $term  = new  SearchLuceneIndexTerm ($pattern, $this->_field);
            $query = new  Wildcard ($term);
            $query->setBoost($this->_boost);

            return $query;
        }

        $tokens =  Analyzer ::getDefault()->tokenize($this->_term, $encoding);

        if (count($tokens) == 0) {
            return new  Insignificant ();
        }

        if (count($tokens) == 1  && !$this->_fuzzyQuery) {
        	$term  = new  SearchLuceneIndexTerm ($tokens[0]->getTermText(), $this->_field);
            $query = new  SearchLuceneSearchQueryTerm ($term);
            $query->setBoost($this->_boost);

            return $query;
        }

        if (count($tokens) == 1  && $this->_fuzzyQuery) {
            $term  = new  SearchLuceneIndexTerm ($tokens[0]->getTermText(), $this->_field);
            $query = new  Fuzzy ($term, $this->_similarity);
            $query->setBoost($this->_boost);

            return $query;
        }

        if ($this->_fuzzyQuery) {
            throw new  QueryParserException ('Fuzzy search is supported only \for non-multiple word terms');
        }
        
        //It's not empty or one term query
        $query = new  MultiTerm ();

        /**
         * @todo Process $token->getPositionIncrement() to support stemming, synonyms and other
         * analizer design features
         */
        foreach ($tokens as $token) {
            $term = new  SearchLuceneIndexTerm ($token->getTermText(), $this->_field);
            $query->addTerm($term, true); // all subterms are required
        }

        $query->setBoost($this->_boost);

        return $query;
    }
}
