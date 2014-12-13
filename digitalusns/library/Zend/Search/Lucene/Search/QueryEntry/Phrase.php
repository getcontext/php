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
use Zend\Search\Lucene\Search\Query\Phrase as SearchLuceneSearchQueryPhrase;
use Zend\Search\Lucene\Search\QueryEntry as QueryEntry;
use Zend\Search\Lucene\Analysis\Analyzer as Analyzer;
use Zend\Search\Lucene\Search\Query\Term as SearchLuceneSearchQueryTerm;
use Zend\Search\Lucene\Index\Term as SearchLuceneIndexTerm;




class  Phrase  extends  QueryEntry 
{
    /**
     * Phrase value
     *
     * @var string
     */
    private $_phrase;

    /**
     * Field
     *
     * @var string|null
     */
    private $_field;


    /**
     * Proximity phrase query
     *
     * @var boolean
     */
    private $_proximityQuery = false;

    /**
     * Words distance, used \for proximiti queries
     *
     * @var integer
     */
    private $_wordsDistance = 0;


    /**
     * Object constractor
     *
     * @param string $phrase
     * @param string $field
     */
    public function __construct($phrase, $field)
    {
        $this->_phrase = $phrase;
        $this->_field  = $field;
    }

    /**
     * Process modifier ('~')
     *
     * @param mixed $parameter
     */
    public function processFuzzyProximityModifier($parameter = null)
    {
        $this->_proximityQuery = true;

        if ($parameter !== null) {
            $this->_wordsDistance = $parameter;
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
        if (strpos($this->_phrase, '?') !== false || strpos($this->_phrase, '*') !== false) {
            throw new  QueryParserException ('Wildcards are only allowed in a single terms.');
        }

        $tokens =  Analyzer ::getDefault()->tokenize($this->_phrase, $encoding);

        if (count($tokens) == 0) {
            return new  Insignificant ();
        }

        if (count($tokens) == 1) {
            $term  = new  SearchLuceneIndexTerm ($tokens[0]->getTermText(), $this->_field);
            $query = new  SearchLuceneSearchQueryTerm ($term);
            $query->setBoost($this->_boost);

            return $query;
        }

        //It's not empty or one term query
        $position = -1;
        $query = new  SearchLuceneSearchQueryPhrase ();
        foreach ($tokens as $token) {
            $position += $token->getPositionIncrement();
            $term = new  SearchLuceneIndexTerm ($token->getTermText(), $this->_field);
            $query->addTerm($term, $position);
        }

        if ($this->_proximityQuery) {
            $query->setSlop($this->_wordsDistance);
        }

        $query->setBoost($this->_boost);

        return $query;
    }
}
