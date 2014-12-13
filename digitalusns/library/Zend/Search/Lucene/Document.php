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
 * @package    \Zend\Search\Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Field  */
require_once 'Zend/Search/Lucene/Field.php';


/**
 * A Document is a set \of fields. Each field has a name and a textual value.
 *
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Exception as SearchLuceneException;
use Zend\Search\Lucene\Field as Field;




class  Document 
{

    /**
     * Associative array  Field  objects where the keys to the
     * array are the names \of the fields.
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Field boost factor
     * It's not stored directly in the index, but affects on normalization factor
     *
     * @var float
     */
    public $boost = 1.0;

    /**
     * Proxy method \for getFieldValue(), provides more convenient access to
     * the string value \of a field.
     *
     * @param  $offset
     * @return string
     */
    public function __get($offset)
    {
        return $this->getFieldValue($offset);
    }


    /**
     * Add a field object to this document.
     *
     * @param  Field  $field
     * @return  Document 
     */
    public function addField( Field  $field)
    {
        $this->_fields[$field->name] = $field;

        return $this;
    }


    /**
     * Return an array with the names \of the fields in this document.
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->_fields);
    }


    /**
     * Returns  Field  object \for a named field in this document.
     *
     * @param string $fieldName
     * @return  Field 
     */
    public function getField($fieldName)
    {
        if (!array_key_exists($fieldName, $this->_fields)) {
            throw new  SearchLuceneException ("Field name \"$fieldName\" not found in document.");
        }
        return $this->_fields[$fieldName];
    }


    /**
     * Returns the string value \of a named field in this document.
     *
     * @see __get()
     * @return string
     */
    public function getFieldValue($fieldName)
    {
        return $this->getField($fieldName)->value;
    }

    /**
     * Returns the string value \of a named field in UTF-8 encoding.
     *
     * @see __get()
     * @return string
     */
    public function getFieldUtf8Value($fieldName)
    {
        return $this->getField($fieldName)->getUtf8Value();
    }
}
