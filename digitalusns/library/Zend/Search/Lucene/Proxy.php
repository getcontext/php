<?php

namespace Zend\Search\Lucene;


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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  SearchLuceneInterface  */
require_once 'Zend/Search/Lucene/Interface.php';


/**
 * Proxy class intended to be used in userland.
 *
 * It tracks, when index object goes out \of scope and forces ndex closing
 *
 * @category   Zend
 * @package     Lucene 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Storage\Directory as Directory;
use Zend\Search\Lucene\Search\QueryHit as QueryHit;
use Zend\Search\Lucene\Index\Term as Term;
use Zend\Search\Lucene\LuceneInterface as SearchLuceneInterface;
use Zend\Search\Lucene\Document as Document;
use Zend\Search\Lucene as Lucene;




class  Proxy  implements  SearchLuceneInterface 
{
    /**
     * Index object
     *
     * @var  SearchLuceneInterface 
     */
    private $_index;

    /**
     * Object constructor
     *
     * @param  SearchLuceneInterface  $index
     */
    public function __construct( SearchLuceneInterface  $index)
    {
        $this->_index = $index;
        $this->_index->addReference();
    }

    /**
     * Object destructor
     */
    public function __destruct()
    {
        if ($this->_index !== null) {
            // This code is invoked if  SearchLuceneInterface  object constructor throws an exception
            $this->_index->removeReference();
        }
        $this->_index = null;
    }

    /**
     * Get current generation number
     *
     * Returns generation number
     * 0 means pre-2.1 index format
     * -1 means there are no segments files.
     *
     * @param  Directory  $directory
     * @return integer
     * @throws  Lucene _Exception
     */
    public static function getActualGeneration( Directory  $directory)
    {
    	 Lucene ::getActualGeneration($directory);
    }

    /**
     * Get segments file name
     *
     * @param integer $generation
     * @return string
     */
    public static function getSegmentFileName($generation)
    {
    	 Lucene ::getSegmentFileName($generation);
    }

    /**
     * Get index format version
     *
     * @return integer
     */
    public function getFormatVersion()
    {
    	return $this->_index->getFormatVersion();
    }

    /**
     * Set index format version.
     * Index is converted to this format at the nearest upfdate time
     *
     * @param int $formatVersion
     * @throws  Lucene _Exception
     */
    public function setFormatVersion($formatVersion)
    {
    	$this->_index->setFormatVersion($formatVersion);
    }

    /**
     * Returns the  Directory  instance \for this index.
     *
     * @return  Directory 
     */
    public function getDirectory()
    {
        return $this->_index->getDirectory();
    }

    /**
     * Returns the total number \of documents in this index (including deleted documents).
     *
     * @return integer
     */
    public function count()
    {
        return $this->_index->count();
    }

    /**
     * Returns one greater than the largest possible document number.
     * This may be used to, e.g., determine how big to allocate a structure which will have
     * an element \for every document number in an index.
     *
     * @return integer
     */
    public function maxDoc()
    {
        return $this->_index->maxDoc();
    }

    /**
     * Returns the total number \of non-deleted documents in this index.
     *
     * @return integer
     */
    public function numDocs()
    {
        return $this->_index->numDocs();
    }

    /**
     * Checks, that document is deleted
     *
     * @param integer $id
     * @return boolean
     * @throws  Lucene _Exception    \Exception is thrown if $id is out \of the range
     */
    public function isDeleted($id)
    {
        return $this->_index->isDeleted($id);
    }

    /**
     * Set default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * Default value is null
     *
     * @param string $fieldName
     */
    public static function setDefaultSearchField($fieldName)
    {
         Lucene ::setDefaultSearchField($fieldName);
    }

    /**
     * Get default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * @return string
     */
    public static function getDefaultSearchField()
    {
        return  Lucene ::getDefaultSearchField();
    }

    /**
     * Set result set limit.
     *
     * 0 (default) means no limit
     *
     * @param integer $limit
     */
    public static function setResultSetLimit($limit)
    {
         Lucene ::setResultSetLimit($limit);
    }

    /**
     * Set result set limit.
     *
     * 0 means no limit
     *
     * @return integer
     */
    public static function getResultSetLimit()
    {
        return  Lucene ::getResultSetLimit();
    }

    /**
     * Retrieve index maxBufferedDocs option
     *
     * maxBufferedDocs is a minimal number \of documents required before
     * the buffered in-memory documents are written into a new Segment
     *
     * Default value is 10
     *
     * @return integer
     */
    public function getMaxBufferedDocs()
    {
        return $this->_index->getMaxBufferedDocs();
    }

    /**
     * Set index maxBufferedDocs option
     *
     * maxBufferedDocs is a minimal number \of documents required before
     * the buffered in-memory documents are written into a new Segment
     *
     * Default value is 10
     *
     * @param integer $maxBufferedDocs
     */
    public function setMaxBufferedDocs($maxBufferedDocs)
    {
        $this->_index->setMaxBufferedDocs($maxBufferedDocs);
    }


    /**
     * Retrieve index maxMergeDocs option
     *
     * maxMergeDocs is a largest number \of documents ever merged by addDocument().
     * Small values (e.g., less than 10,000) are best \for interactive indexing,
     * as this limits the length \of pauses while indexing to a few seconds.
     * Larger values are best \for batched indexing and speedier searches.
     *
     * Default value is PHP_INT_MAX
     *
     * @return integer
     */
    public function getMaxMergeDocs()
    {
        return $this->_index->getMaxMergeDocs();
    }

    /**
     * Set index maxMergeDocs option
     *
     * maxMergeDocs is a largest number \of documents ever merged by addDocument().
     * Small values (e.g., less than 10,000) are best \for interactive indexing,
     * as this limits the length \of pauses while indexing to a few seconds.
     * Larger values are best \for batched indexing and speedier searches.
     *
     * Default value is PHP_INT_MAX
     *
     * @param integer $maxMergeDocs
     */
    public function setMaxMergeDocs($maxMergeDocs)
    {
        $this->_index->setMaxMergeDocs($maxMergeDocs);
    }


    /**
     * Retrieve index mergeFactor option
     *
     * mergeFactor determines how often segment indices are merged by addDocument().
     * With smaller values, less RAM is used while indexing,
     * and searches on unoptimized indices are faster,
     * but indexing speed is slower.
     * With larger values, more RAM is used during indexing,
     * and while searches on unoptimized indices are slower,
     * indexing is faster.
     * Thus larger values (> 10) are best \for batch index creation,
     * and smaller values (< 10) \for indices that are interactively maintained.
     *
     * Default value is 10
     *
     * @return integer
     */
    public function getMergeFactor()
    {
        return $this->_index->getMergeFactor();
    }

    /**
     * Set index mergeFactor option
     *
     * mergeFactor determines how often segment indices are merged by addDocument().
     * With smaller values, less RAM is used while indexing,
     * and searches on unoptimized indices are faster,
     * but indexing speed is slower.
     * With larger values, more RAM is used during indexing,
     * and while searches on unoptimized indices are slower,
     * indexing is faster.
     * Thus larger values (> 10) are best \for batch index creation,
     * and smaller values (< 10) \for indices that are interactively maintained.
     *
     * Default value is 10
     *
     * @param integer $maxMergeDocs
     */
    public function setMergeFactor($mergeFactor)
    {
        $this->_index->setMergeFactor($mergeFactor);
    }

    /**
     * Performs a query against the index and returns an array
     * \of  QueryHit  objects.
     * Input is a string or  Lucene _Search_Query.
     *
     * @param mixed $query
     * @return array  QueryHit 
     * @throws  Lucene _Exception
     */
    public function find($query)
    {
        // actual parameter list
        $parameters = func_get_args();

        // invoke $this->_index->find() method with specified parameters
        return call_user_func_array(array(&$this->_index, 'find'), $parameters);
    }

    /**
     * Returns a list \of all unique field names that exist in this index.
     *
     * @param boolean $indexed
     * @return array
     */
    public function getFieldNames($indexed = false)
    {
        return $this->_index->getFieldNames($indexed);
    }

    /**
     * Returns a  Document  object \for the document
     * number $id in this index.
     *
     * @param integer| QueryHit  $id
     * @return  Document 
     */
    public function getDocument($id)
    {
        return $this->_index->getDocument($id);
    }

    /**
     * Returns true if index contain documents with specified term.
     *
     * Is used \for query optimization.
     *
     * @param  Term  $term
     * @return boolean
     */
    public function hasTerm( Term  $term)
    {
        return $this->_index->hasTerm($term);
    }

    /**
     * Returns IDs \of all the documents containing term.
     *
     * @param  Term  $term
     * @return array
     */
    public function termDocs( Term  $term)
    {
        return $this->_index->termDocs($term);
    }

    /**
     * Returns an array \of all term freqs.
     * Return array structure: array( docId => freq, ...)
     *
     * @param  Term  $term
     * @return integer
     */
    public function termFreqs( Term  $term)
    {
        return $this->_index->termFreqs($term);
    }

    /**
     * Returns an array \of all term positions in the documents.
     * Return array structure: array( docId => array( pos1, pos2, ...), ...)
     *
     * @param  Term  $term
     * @return array
     */
    public function termPositions( Term  $term)
    {
        return $this->_index->termPositions($term);
    }

    /**
     * Returns the number \of documents in this index containing the $term.
     *
     * @param  Term  $term
     * @return integer
     */
    public function docFreq( Term  $term)
    {
        return $this->_index->docFreq($term);
    }

    /**
     * Retrive similarity used by index reader
     *
     * @return  Lucene _Search_Similarity
     */
    public function getSimilarity()
    {
        return $this->_index->getSimilarity();
    }

    /**
     * Returns a normalization factor \for "field, document" pair.
     *
     * @param integer $id
     * @param string $fieldName
     * @return float
     */
    public function norm($id, $fieldName)
    {
        return $this->_index->norm($id, $fieldName);
    }

    /**
     * Returns true if any documents have been deleted from this index.
     *
     * @return boolean
     */
    public function hasDeletions()
    {
        return $this->_index->hasDeletions();
    }

    /**
     * Deletes a document from the index.
     * $id is an internal document id
     *
     * @param integer| QueryHit  $id
     * @throws  Lucene _Exception
     */
    public function delete($id)
    {
        return $this->_index->delete($id);
    }

    /**
     * Adds a document to this index.
     *
     * @param  Document  $document
     */
    public function addDocument( Document  $document)
    {
        $this->_index->addDocument($document);
    }

    /**
     * Commit changes resulting from delete() or undeleteAll() operations.
     */
    public function commit()
    {
        $this->_index->commit();
    }

    /**
     * Optimize index.
     *
     * Merges all segments into one
     */
    public function optimize()
    {
        $this->_index->optimize();
    }

    /**
     * Returns an array \of all terms in this index.
     *
     * @return array
     */
    public function terms()
    {
        return $this->_index->terms();
    }


    /**
     * Reset terms stream.
     */
    public function resetTermsStream()
    {
        $this->_index->resetTermsStream();
    }

    /**
     * Skip terms stream up to specified term preffix.
     *
     * Prefix contains fully specified field info and portion \of searched term
     *
     * @param  Term  $prefix
     */
    public function skipTo( Term  $prefix)
    {
        return $this->_index->skipTo($prefix);
    }

    /**
     * Scans terms dictionary and returns next term
     *
     * @return  Term |null
     */
    public function nextTerm()
    {
        return $this->_index->nextTerm();
    }

    /**
     * Returns term in current position
     *
     * @return  Term |null
     */
    public function currentTerm()
    {
        return $this->_index->currentTerm();
    }

    /**
     * Close terms stream
     *
     * Should be used \for resources clean up if stream is not read up to the end
     */
    public function closeTermsStream()
    {
        $this->_index->closeTermsStream();
    }


    /**
     * Undeletes all documents currently marked as deleted in this index.
     */
    public function undeleteAll()
    {
        return $this->_index->undeleteAll();
    }

    /**
     * Add reference to the index object
     *
     * @internal
     */
    public function addReference()
    {
        return $this->_index->addReference();
    }

    /**
     * Remove reference from the index object
     *
     * When reference count becomes zero, index is closed and resources are cleaned up
     *
     * @internal
     */
    public function removeReference()
    {
        return $this->_index->removeReference();
    }
}
