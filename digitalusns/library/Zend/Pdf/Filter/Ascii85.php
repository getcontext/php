<?php

namespace Zend\Pdf\Filter;


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


/**  PdfFilterInterface  */
require_once 'Zend/Pdf/Filter/Interface.php';


/**
 * ASCII85 stream filter
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Filter\FilterInterface as PdfFilterInterface;
use Zend\Pdf\Exception as PdfException;




class  Ascii85  implements  PdfFilterInterface 
{
    /**
     * Encode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws  PdfException 
     */
    public static function encode($data, $params = null)
    {
        throw new  PdfException ('Not implemented yet');
    }

    /**
     * Decode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws  PdfException 
     */
    public static function decode($data, $params = null)
    {
        throw new  PdfException ('Not implemented yet');
    }
}
