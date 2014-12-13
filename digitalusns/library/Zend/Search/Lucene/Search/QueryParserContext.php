<?php

namespace Zend\Search\Lucene\Search;


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
 * @package     Lucene 
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Lucene _FSM */
require_once 'Zend/Search/Lucene/FSM.php';


/**  Lucene _Index_Term */
require_once 'Zend/Search/Lucene/Index/Term.php';

/**  QueryToken  */
require_once 'Zend/Search/Lucene/Search/QueryToken.php';

/**  Query _Term */
require_once 'Zend/Search/Lucene/Search/Query/Term.php';

/**  Query _MultiTerm */
require_once 'Zend/Search/Lucene/Search/Query/MultiTerm.php';

/**  SearchLuceneSearchQueryBoolean  */
require_once 'Zend/Search/Lucene/Search/Query/Boolean.php';

/**  Query _Phrase */
require_once 'Zend/Search/Lucene/Search/Query/Phrase.php';

/**  SearchLuceneException  */
require_once 'Zend/Search/Lucene/Exception.php';

/**  QueryParserException  */
require_once 'Zend/Search/Lucene/Search/QueryParserException.php';

/**  BooleanExpressionRecognizer  */
require_once 'Zend/Search/Lucene/Search/BooleanExpressionRecognizer.php';

/**  QueryEntry  */
require_once 'Zend/Search/Lucene/Search/QueryEntry.php';


/**
 * @category   Zend
 * @package     Lucene 
 * @subpackage Search
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Search\BooleanExpressionRecognizer as BooleanExpressionRecognizer;
use Zend\Search\Lucene\Search\QueryParserException as QueryParserException;
use Zend\Search\Lucene\Search\Query\Insignificant as Insignificant;
use Zend\Search\Lucene\Search\Query\QueryBoolean as SearchLuceneSearchQueryBoolean;
use Zend\Search\Lucene\Search\QueryParser as QueryParser;
use Zend\Search\Lucene\Search\QueryToken as QueryToken;
use Zend\Search\Lucene\Search\QueryEntry as QueryEntry;
use Zend\Search\Lucene\Search\Query as Query;
use Zend\Search\Lucene\Exception as SearchLuceneException;
use Zend\Search\Exception as SearchException;
use Zend\Search\Lucene as Lucene;
use B\BOR as BOR;




class  QueryParserContext 
{
    /**
     * Default field \for the context.
     *
     * null means, that term should be searched through all fields
     *  Query ::rewriteQuery($index) transletes such queries to several
     *
     * @var string|null
     */
    private $_defaultField;

    /**
     * Field specified \for next entry
     *
     * @var string
     */
    private $_nextEntryField = null;

    /**
     * True means, that term is required.
     * False means, that term is prohibited.
     * null means, that term is neither prohibited, nor required
     *
     * @var boolean
     */
    private $_nextEntrySign = null;


    /**
     * Entries grouping mode
     */
    const GM_SIGNS   = 0;  // Signs mode: '+term1 term2 -term3 +(subquery1) -(subquery2)'
    const GM_BOOLEAN = 1;  // Boolean operators mode: 'term1 and term2  or  (subquery1) and not (subquery2)'

    /**
     * Grouping mode
     *
     * @var integer
     */
    private $_mode = null;

    /**
     * Entries signs.
     * Used in GM_SIGNS grouping mode
     *
     * @var arrays
     */
    private $_signs = array();

    /**
     * Query entries
     * Each entry is a  QueryEntry  object or
     * boolean operator ( QueryToken  class constant)
     *
     * @var array
     */
    private $_entries = array();

    /**
     * Query string encoding
     *
     * @var string
     */
    private $_encoding;


    /**
     * Context object constructor
     *
     * @param string $encoding
     * @param string|null $defaultField
     */
    public function __construct($encoding, $defaultField = null)
    {
        $this->_encoding     = $encoding;
        $this->_defaultField = $defaultField;
    }


    /**
     * Get context default field
     *
     * @return string|null
     */
    public function getField()
    {
        return ($this->_nextEntryField !== null)  ?  $this->_nextEntryField : $this->_defaultField;
    }

    /**
     * Set field \for next entry
     *
     * @param string $field
     */
    public function setNextEntryField($field)
    {
        $this->_nextEntryField = $field;
    }


    /**
     * Set sign \for next entry
     *
     * @param integer $sign
     * @throws  SearchLuceneException 
     */
    public function setNextEntrySign($sign)
    {
        if ($this->_mode === self::GM_BOOLEAN) {
            throw new  QueryParserException ('It\'s not allowed to mix boolean and signs styles in the same subquery.');
        }

        $this->_mode = self::GM_SIGNS;

        if ($sign ==  QueryToken ::TT_REQUIRED) {
            $this->_nextEntrySign = true;
        } else if ($sign ==  QueryToken ::TT_PROHIBITED) {
            $this->_nextEntrySign = false;
        } else {
            throw new  SearchLuceneException ('Unrecognized sign type.');
        }
    }


    /**
     * Add entry to a query
     *
     * @param  QueryEntry  $entry
     */
    public function addEntry( QueryEntry  $entry)
    {
        if ($this->_mode !== self::GM_BOOLEAN) {
            $this->_signs[] = $this->_nextEntrySign;
        }

        $this->_entries[] = $entry;

        $this->_nextEntryField = null;
        $this->_nextEntrySign  = null;
    }


    /**
     * Process fuzzy search or proximity search modifier
     *
     * @throws  QueryParserException 
     */
    public function processFuzzyProximityModifier($parameter = null)
    {
        // Check, that modifier has came just after word or phrase
        if ($this->_nextEntryField !== null  ||  $this->_nextEntrySign !== null) {
            throw new  QueryParserException ('\'~\' modifier must follow word or phrase.');
        }

        $lastEntry = array_pop($this->_entries);

        if (!$lastEntry instanceof  QueryEntry ) {
            // there are no entries or last entry is boolean operator
            throw new  QueryParserException ('\'~\' modifier must follow word or phrase.');
        }

        $lastEntry->processFuzzyProximityModifier($parameter);

        $this->_entries[] = $lastEntry;
    }

    /**
     * Set boost factor to the entry
     *
     * @param float $boostFactor
     */
    public function boost($boostFactor)
    {
        // Check, that modifier has came just after word or phrase
        if ($this->_nextEntryField !== null  ||  $this->_nextEntrySign !== null) {
            throw new  QueryParserException ('\'^\' modifier must follow word, phrase or subquery.');
        }

        $lastEntry = array_pop($this->_entries);

        if (!$lastEntry instanceof  QueryEntry ) {
            // there are no entries or last entry is boolean operator
            throw new  QueryParserException ('\'^\' modifier must follow word, phrase or subquery.');
        }

        $lastEntry->boost($boostFactor);

        $this->_entries[] = $lastEntry;
    }

    /**
     * Process logical operator
     *
     * @param integer $operator
     */
    public function addLogicalOperator($operator)
    {
        if ($this->_mode === self::GM_SIGNS) {
            throw new  QueryParserException ('It\'s not allowed to mix boolean and signs styles in the same subquery.');
        }

        $this->_mode = self::GM_BOOLEAN;

        $this->_entries[] = $operator;
    }


    /**
     * Generate 'signs style' query from the context
     * '+term1 term2 -term3 +(<subquery1>) ...'
     *
     * @return  Query 
     */
    public function _signStyleExpressionQuery()
    {
        $query = new  SearchLuceneSearchQueryBoolean ();

        if ( QueryParser ::getDefaultOperator() ==  QueryParser ::B_AND) {
            $defaultSign = true; // required
        } else {
            //  QueryParser :: BOR 
            $defaultSign = null; // optional
        }

        foreach ($this->_entries as $entryId => $entry) {
            $sign = ($this->_signs[$entryId] !== null) ?  $this->_signs[$entryId] : $defaultSign;
            $query->addSubquery($entry->getQuery($this->_encoding), $sign);
        }

        return $query;
    }


    /**
     * Generate 'boolean style' query from the context
     * 'term1 and term2   or   term3 and (<subquery1>) and not (<subquery2>)'
     *
     * @return  Query 
     * @throws  Lucene 
     */
    private function _booleanExpressionQuery()
    {
        /**
         * We treat each level \of an expression as a boolean expression in
         * a Disjunctive Normal Form
         *
         * AND operator has higher precedence than OR
         *
         * Thus logical query is a disjunction \of one or more conjunctions \of
         * one or more query entries
         */

        $expressionRecognizer = new  BooleanExpressionRecognizer ();

        try {
            foreach ($this->_entries as $entry) {
                if ($entry instanceof  QueryEntry ) {
                    $expressionRecognizer->processLiteral($entry);
                } else {
                    switch ($entry) {
                        case  QueryToken ::TT_AND_LEXEME:
                            $expressionRecognizer->processOperator( BooleanExpressionRecognizer ::IN_AND_OPERATOR);
                            break;

                        case  QueryToken ::TT_OR_LEXEME:
                            $expressionRecognizer->processOperator( BooleanExpressionRecognizer ::IN_OR_OPERATOR);
                            break;

                        case  QueryToken ::TT_NOT_LEXEME:
                            $expressionRecognizer->processOperator( BooleanExpressionRecognizer ::IN_NOT_OPERATOR);
                            break;

                        default:
                            throw new  Lucene ('Boolean expression error. Unknown operator type.');
                    }
                }
            }

            $conjuctions = $expressionRecognizer->finishExpression();
        } catch ( SearchException  $e) {
            // throw new  QueryParserException ('Boolean expression error. Error message: \'' .
            //                                                          $e->getMessage() . '\'.' );
            // It's query syntax error message and it should be user friendly. So FSM message is omitted
            throw new  QueryParserException ('Boolean expression error.');
        }

        // Remove 'only negative' conjunctions
        foreach ($conjuctions as $conjuctionId => $conjuction) {
            $nonNegativeEntryFound = false;

            foreach ($conjuction as $conjuctionEntry) {
                if ($conjuctionEntry[1]) {
                    $nonNegativeEntryFound = true;
                    break;
                }
            }

            if (!$nonNegativeEntryFound) {
                unset($conjuctions[$conjuctionId]);
            }
        }


        $subqueries = array();
        foreach ($conjuctions as  $conjuction) {
            // Check, if it's a one term conjuction
            if (count($conjuction) == 1) {
                $subqueries[] = $conjuction[0][0]->getQuery($this->_encoding);
            } else {
                $subquery = new  SearchLuceneSearchQueryBoolean ();

                foreach ($conjuction as $conjuctionEntry) {
                    $subquery->addSubquery($conjuctionEntry[0]->getQuery($this->_encoding), $conjuctionEntry[1]);
                }

                $subqueries[] = $subquery;
            }
        }

        if (count($subqueries) == 0) {
            return new  Insignificant ();
        }

        if (count($subqueries) == 1) {
            return $subqueries[0];
        }


        $query = new  SearchLuceneSearchQueryBoolean ();

        foreach ($subqueries as $subquery) {
            // Non-requirered entry/subquery
            $query->addSubquery($subquery);
        }

        return $query;
    }

    /**
     * Generate query from current context
     *
     * @return  Query 
     */
    public function getQuery()
    {
        if ($this->_mode === self::GM_BOOLEAN) {
            return $this->_booleanExpressionQuery();
        } else {
            return $this->_signStyleExpressionQuery();
        }
    }
}
