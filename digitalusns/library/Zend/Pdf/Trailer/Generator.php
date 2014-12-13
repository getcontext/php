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
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Trailer  */
require_once 'Zend/Pdf/Const.php';

/**  Trailer  */
require_once 'Zend/Pdf/Trailer.php';


/**
 * PDF file trailer generator (used \for just created PDF)
 *
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Dictionary as Dictionary;
use Zend\Pdf\Trailer as Trailer;
use Zend\Pdf as Pdf;




class  Generator  extends  Trailer 
{
    /**
     * Object constructor
     *
     * @param  Dictionary  $dict
     */
    public function __construct( Dictionary  $dict)
    {
        parent::__construct($dict);
    }

    /**
     * Get length \of source PDF
     *
     * @return string
     */
    public function getPDFLength()
    {
        return strlen( Pdf ::PDF_HEADER);
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return  Pdf ::PDF_HEADER;
    }

    /**
     * Get header \of free objects list
     * Returns object number \of last free object
     *
     * @return integer
     */
    public function getLastFreeObject()
    {
        return 0;
    }
}
