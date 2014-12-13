<?php

namespace Zend\Pdf\Parser;


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


/**  Parser  */
require_once 'Zend/Pdf/Parser.php';


/**
 * PDF object stream parser
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\ElementFactory as ElementFactory;
use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\PhpArray as PhpArray;
use Zend\Pdf\Parser as Parser;




class  Stream  extends  Parser 
{
    /**
     * Get Trailer object
     *
     * @return \Zend\Pdf\Trailer\Keeper
     */
    public function getTrailer()
    {
        throw new  PdfException ('Stream object parser doesn\'t contain trailer information.');
    }

    /**
     * Object constructor
     *
     * @param string $pdfString
     * @param  ElementFactory  $factory
     * @throws \Zend_Exception
     */
    public function __construct(&$source,  ElementFactory  $factory)
    {
        $this->_current        = 0;
        $this->_currentContext = null;
        $this->_contextStack   = array();
        $this->_elements       = new  PhpArray ();
        $this->_objFactory     = $factory;
    }
}
