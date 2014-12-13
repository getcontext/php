<?php

namespace Zend\Pdf\Filter\Compression;


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


/**  Compression  */
require_once 'Zend/Pdf/Filter/Compression.php';


/**
 * Flate stream filter
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Filter\Compression as Compression;
use Zend\Pdf\Exception as PdfException;




class  Flate  extends  Compression 
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
        if ($params != null) {
            $data = self::_applyEncodeParams($data, $params);
        }

        if (extension_loaded('zlib')) {
            $trackErrors = ini_get( "track_errors");
            ini_set('track_errors', '1');

            if (($output = @gzcompress($data)) === false) {
                ini_set('track_errors', $trackErrors);
                throw new  PdfException ($php_errormsg);
            }

            ini_set('track_errors', $trackErrors);
        } else {
            throw new  PdfException ('Not implemented yet. You have to use zlib extension.');
        }

        return $output;
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
        global $php_errormsg;

        if (extension_loaded('zlib')) {
            $trackErrors = ini_get( "track_errors");
            ini_set('track_errors', '1');

            if (($output = @gzuncompress($data)) === false) {
                ini_set('track_errors', $trackErrors);
                throw new  PdfException ($php_errormsg);
            }

            ini_set('track_errors', $trackErrors);
        } else {
            throw new  PdfException ('Not implemented yet');
        }

        if ($params !== null) {
            return self::_applyDecodeParams($output, $params);
        } else {
            return $output;
        }
    }
}
