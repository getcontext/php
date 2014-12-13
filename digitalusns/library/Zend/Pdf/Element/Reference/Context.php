<?php

namespace Zend\Pdf\Element\Reference;


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
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  StringParser  */
require_once 'Zend/Pdf/StringParser.php';

/**  Table  */
require_once 'Zend/Pdf/Element/Reference/Table.php';


/**
 * PDF reference object context
 * \Reference context is defined by PDF parser and PDF Refernce table
 *
 * @category   Zend
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Reference\Table as Table;
use Zend\Pdf\StringParser as StringParser;




class  Context 
{
    /**
     * PDF parser object.
     *
     * @var \Zend\Pdf\Parser
     */
    private $_stringParser;

    /**
     * \Reference table
     *
     * @var  Table 
     */
    private $_refTable;

    /**
     * Object constructor
     *
     * @param  StringParser  $parser
     * @param  Table  $refTable
     */
    public function __construct( StringParser  $parser,
                                 Table  $refTable)
    {
        $this->_stringParser = $parser;
        $this->_refTable     = $refTable;
    }


    /**
     * Context parser
     *
     * @return \Zend\Pdf\Parser
     */
    public function getParser()
    {
        return $this->_stringParser;
    }


    /**
     * Context reference table
     *
     * @return  Table 
     */
    public function getRefTable()
    {
        return $this->_refTable;
    }
}

