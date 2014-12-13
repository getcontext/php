<?php

namespace Zend\Search\Lucene\Index\SegmentWriter;


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
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  SearchLuceneException  */
require_once 'Zend/Search/Lucene/Exception.php';

/**  Analyzer  */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';

/**  SegmentWriter  */
require_once 'Zend/Search/Lucene/Index/SegmentWriter.php';


/**
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Index\SegmentWriter as SegmentWriter;
use Zend\Search\Lucene\Search\Similarity as Similarity;
use Zend\Search\Lucene\Analysis\Analyzer as Analyzer;
use Zend\Search\Lucene\Storage\Directory as Directory;
use Zend\Search\Lucene\Index\SegmentInfo as SegmentInfo;
use Zend\Search\Lucene\Index\Term as Term;
use Zend\Search\Lucene\Exception as SearchLuceneException;
use Zend\Search\Lucene\Document as Document;




class  DocumentWriter  extends  SegmentWriter 
{
    /**
     * Term Dictionary
     * Array \of the  Term  objects
     * Corresponding  Term Info object stored in the $_termDictionaryInfos
     *
     * @var array
     */
    protected $_termDictionary;

    /**
     * Documents, which contain the term
     *
     * @var array
     */
    protected $_termDocs;

    /**
     * Object constructor.
     *
     * @param  Directory  $directory
     * @param string $name
     */
    public function __construct( Directory  $directory, $name)
    {
        parent::__construct($directory, $name);

        $this->_termDocs       = array();
        $this->_termDictionary = array();
    }


    /**
     * Adds a document to this segment.
     *
     * @param  Document  $document
     * @throws  SearchLuceneException 
     */
    public function addDocument( Document  $document)
    {
        $storedFields = array();
        $docNorms     = array();
        $similarity   =  Similarity ::getDefault();

        foreach ($document->getFieldNames() as $fieldName) {
            $field = $document->getField($fieldName);
            $this->addField($field);

            if ($field->storeTermVector) {
                /**
                 * @todo term vector storing support
                 */
                throw new  SearchLuceneException ('Store term vector functionality is not supported yet.');
            }

            if ($field->isIndexed) {
                if ($field->isTokenized) {
                    $analyzer =  Analyzer ::getDefault();
                    $analyzer->setInput($field->value, $field->encoding);

                    $position     = 0;
                    $tokenCounter = 0;
                    while (($token = $analyzer->nextToken()) !== null) {
                        $tokenCounter++;

                        $term = new  Term ($token->getTermText(), $field->name);
                        $termKey = $term->key();

                        if (!isset($this->_termDictionary[$termKey])) {
                            // New term
                            $this->_termDictionary[$termKey] = $term;
                            $this->_termDocs[$termKey] = array();
                            $this->_termDocs[$termKey][$this->_docCount] = array();
                        } else if (!isset($this->_termDocs[$termKey][$this->_docCount])) {
                            // Existing term, but new term entry
                            $this->_termDocs[$termKey][$this->_docCount] = array();
                        }
                        $position += $token->getPositionIncrement();
                        $this->_termDocs[$termKey][$this->_docCount][] = $position;
                    }

                    $docNorms[$field->name] = chr($similarity->encodeNorm( $similarity->lengthNorm($field->name,
                                                                                                   $tokenCounter)*
                                                                           $document->boost*
                                                                           $field->boost ));
                } else {
                    $term = new  Term ($field->getUtf8Value(), $field->name);
                    $termKey = $term->key();

                    if (!isset($this->_termDictionary[$termKey])) {
                        // New term
                        $this->_termDictionary[$termKey] = $term;
                        $this->_termDocs[$termKey] = array();
                        $this->_termDocs[$termKey][$this->_docCount] = array();
                    } else if (!isset($this->_termDocs[$termKey][$this->_docCount])) {
                        // Existing term, but new term entry
                        $this->_termDocs[$termKey][$this->_docCount] = array();
                    }
                    $this->_termDocs[$termKey][$this->_docCount][] = 0; // position

                    $docNorms[$field->name] = chr($similarity->encodeNorm( $similarity->lengthNorm($field->name, 1)*
                                                                           $document->boost*
                                                                           $field->boost ));
                }
            }

            if ($field->isStored) {
                $storedFields[] = $field;
            }
        }


        foreach ($this->_fields as $fieldName => $field) {
            if (!$field->isIndexed) {
                continue;
            }

            if (!isset($this->_norms[$fieldName])) {
                $this->_norms[$fieldName] = str_repeat(chr($similarity->encodeNorm( $similarity->lengthNorm($fieldName, 0) )),
                                                       $this->_docCount);
            }

            if (isset($docNorms[$fieldName])){
                $this->_norms[$fieldName] .= $docNorms[$fieldName];
            } else {
                $this->_norms[$fieldName] .= chr($similarity->encodeNorm( $similarity->lengthNorm($fieldName, 0) ));
            }
        }

        $this->addStoredFields($storedFields);
    }


    /**
     * Dump Term Dictionary (.tis) and Term Dictionary Index (.tii) segment files
     */
    protected function _dumpDictionary()
    {
        ksort($this->_termDictionary, SORT_STRING);

        $this->initializeDictionaryFiles();

        foreach ($this->_termDictionary as $termId => $term) {
            $this->addTerm($term, $this->_termDocs[$termId]);
        }

        $this->closeDictionaryFiles();
    }


    /**
     * Close segment, write it to disk and return segment info
     *
     * @return  SegmentInfo 
     */
    public function close()
    {
        if ($this->_docCount == 0) {
            return null;
        }

        $this->_dumpFNM();
        $this->_dumpDictionary();

        $this->_generateCFS();

        return new  SegmentInfo ($this->_directory,
                                                        $this->_name,
                                                        $this->_docCount,
                                                        -1,
                                                        null,
                                                        true,
                                                        true);
    }

}

