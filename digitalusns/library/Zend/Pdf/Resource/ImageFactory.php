<?php

namespace Zend\Pdf\Resource;


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


/** \Zend\Pdf */
require_once 'Zend/Pdf.php';


/**
 * \Zend\Pdf\ImageFactory
 *
 * Helps manage the diverse set \of supported image file types.
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Use \Zend\Mime not file extension \for type determination.
 */


use Zend\Pdf\Resource\Image\Tiff as Tiff;
use Zend\Pdf\Resource\Image\Jpeg as Jpeg;
use Zend\Pdf\Resource\Image\Png as Png;
use Zend\Pdf\Exception as PdfException;




class  ImageFactory 
{
    public static function factory($filename) {
        if(!is_file($filename)) {
            throw new  PdfException ("Cannot create image resource. File not found.");
        }
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        /*
         * There are plans to use \Zend\Mime and not file extension. In the mean time, if you need to
         * use an alternate file extension just spin up the right processor directly.
         */
        switch (strtolower($extension)) {
            case 'tif':
                //Fall through to next case;
            case 'tiff':
                return new  Tiff ($filename);
                break;
            case 'png':
                return new  Png ($filename);
                break;
            case 'jpg':
                //Fall through to next case;
            case 'jpe':
                //Fall through to next case;
            case 'jpeg':
                return new  Jpeg ($filename);
                break;
            default:
                throw new  PdfException ("Cannot create image resource. File extension not known or unsupported type.");
                break;
        }
    }
}

