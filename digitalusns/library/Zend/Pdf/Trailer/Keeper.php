<?php

namespace Zend\Pdf\Trailer;


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
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Trailer  */
require_once 'Zend/Pdf/Trailer.php';

/**  Context  */
require_once 'Zend/Pdf/Element/Reference/Context.php';


/**
 * PDF file trailer.
 * Stores and provides access to the trailer parced from a PDF file
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Reference\Context as Context;
use Zend\Pdf\Element\Dictionary as Dictionary;
use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\Trailer as Trailer;




class  Keeper  extends  Trailer 
{
    /**
     * \Reference context
     *
     * @var  Context 
     */
    private $_context;

    /**
     * Previous trailer
     *
     * @var  Trailer 
     */
    private $_prev;


    /**
     * Object constructor
     *
     * @param  Dictionary  $dict
     * @param  Context  $context
     * @param  Trailer  $prev
     */
    public function __construct( Dictionary  $dict,
                                 Context  $context,
                                $prev = null)
    {
        parent::__construct($dict);

        $this->_context = $context;
        $this->_prev    = $prev;
    }

    /**
     * Setter \for $this->_prev
     *
     * @param  Keeper  $prev
     */
    public function setPrev( Keeper  $prev)
    {
        $this->_prev = $prev;
    }

    /**
     * Getter \for $this->_prev
     *
     * @return  Trailer 
     */
    public function getPrev()
    {
        return $this->_prev;
    }

    /**
     * Get length \of source PDF
     *
     * @return string
     */
    public function getPDFLength()
    {
        return $this->_context->getParser()->getLength();
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return $this->_context->getParser()->getString();
    }

    /**
     * Get reference table, which corresponds to the trailer.
     * Proxy to the $_context member methad call
     *
     * @return  Context 
     */
    public function getRefTable()
    {
        return $this->_context->getRefTable();
    }

    /**
     * Get header \of free objects list
     * Returns object number \of last free object
     *
     * @throws  PdfException 
     * @return integer
     */
    public function getLastFreeObject()
    {
        try {
            $this->_context->getRefTable()->getNextFree('0 65535 R');
        } catch ( PdfException  $e) {
            if ($e->getMessage() == 'Object not found.') {
                /**
                 * Here is work around \for some wrong generated PDFs.
                 * We have not found reference to the header \of free object list,
                 * thus we treat it as there are no free objects.
                 */
                return 0;
            }

            throw $e;
        }
    }
}
