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
use Zend\Search\Lucene\Analysis\Analyzer as Analyzer;
use Zend\Search\Lucene\Search\Query\Term as SearchLuceneSearchQueryTerm;
use Zend\Search\Lucene\Document\Html as Html;
use Zend\Search\Lucene\Search\Query as Query;
use Zend\Search\Lucene\Index\Term as SearchLuceneIndexTerm;
use Zend\Search\Lucene\LuceneInterface as SearchLuceneInterface;
use Zend\Search\Lucene\Exception as SearchLuceneException;




class  Wildcard  extends  Query 
{
    /**
     * Search pattern.
     *
     * Field has to be fully specified or has to be null
     * Text may contain '*' or '?' symbols
     *
     * @var  SearchLuceneIndexTerm 
     */
    private $_pattern;

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
    private $_matches = null;

    /**
     *  Wildcard  constructor.
     *
     * @param  SearchLuceneIndexTerm  $pattern
     */
    public function __construct( SearchLuceneIndexTerm  $pattern)
    {
        $this->_pattern = $pattern;
    }

    /**
     * Get terms prefix
     *
     * @param string $word
     * @return string
     */
    private static function _getPrefix($word)
    {
        $questionMarkPosition = strpos($word, '?');
        $astrericPosition     = strpos($word, '*');

        if ($questionMarkPosition !== false) {
            if ($astrericPosition !== false) {
                return substr($word, 0, min($questionMarkPosition, $astrericPosition));
            }

            return substr($word, 0, $questionMarkPosition);
        } else if ($astrericPosition !== false) {
            return substr($word, 0, $astrericPosition);
        }

        return $word;
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

        if ($this->_pattern->field === null) {
            // Search through all fields
            $fields = $index->getFieldNames(true /* indexed fields list */);
        } else {
            $fields = array($this->_pattern->field);
        }

        $prefix          = self::_getPrefix($this->_pattern->text);
        $prefixLength    = strlen($prefix);
        $matchExpression = '/^' . str_replace(array('\\?', '\\*'), array('.', '.*') , preg_quote($this->_pattern->text, '/')) . '$/';

        /** @todo check \for PCRE unicode support may be performed through Zend_Environment in some future */
        if (@preg_match('/\pL/u', 'a') == 1) {
            // PCRE unicode support is turned on
            // add Unicode modifier to the match expression
            $matchExpression .= 'u';
        }


        foreach ($fields as $field) {
            $index->resetTermsStream();

            if ($prefix != '') {
                $index->skipTo(new  SearchLuceneIndexTerm ($prefix, $field));

                while ($index->currentTerm() !== null          &&
                       $index->currentTerm()->field == $field  &&
                       substr($index->currentTerm()->text, 0, $prefixLength) == $prefix) {
                    if (preg_match($matchExpression, $index->currentTerm()->text) === 1) {
                        $this->_matches[] = $index->currentTerm();
                    }

                    $index->nextTerm();
                }
            } else {
                $index->skipTo(new  SearchLuceneIndexTerm ('', $field));

                while ($index->currentTerm() !== null  &&  $index->currentTerm()->field == $field) {
                    if (preg_match($matchExpression, $index->currentTerm()->text) === 1) {
                        $this->_matches[] = $index->currentTerm();
                    }

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
        throw new  SearchLuceneException ('Wildcard query should not be directly used \for search. Use $query->rewrite($index)');
    }


    /**
     * Returns query pattern
     *
     * @return  SearchLuceneIndexTerm 
     */
    public function getPattern()
    {
        return $this->_pattern;
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
        throw new  SearchLuceneException ('Wildcard query should not be directly used \for search. Use $query->rewrite($index)');
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
        throw new  SearchLuceneException ('Wildcard query should not be directly used \for search. Use $query->rewrite($index)');
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
        throw new  SearchLuceneException ('Wildcard query should not be directly used \for search. Use $query->rewrite($index)');
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
        throw new  SearchLuceneException ('Wildcard query should not be directly used \for search. Use $query->rewrite($index)');
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

        $matchExpression = '/^' . str_replace(array('\\?', '\\*'), array('.', '.*') , preg_quote($this->_pattern->text, '/')) . '$/';
        if (@preg_match('/\pL/u', 'a') == 1) {
            // PCRE unicode support is turned on
            // add Unicode modifier to the match expression
            $matchExpression .= 'u';
        }

        $tokens =  Analyzer ::getDefault()->tokenize($doc->getFieldUtf8Value('body'), 'UTF-8');
        foreach ($tokens as $token) {
            if (preg_match($matchExpression, $token->getTermText()) === 1) {
                $words[] = $token->getTermText();
            }
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
        return (($this->_pattern->field === null)? '' : $this->_pattern->field . ':') . $this->_pattern->text;
    }
}

