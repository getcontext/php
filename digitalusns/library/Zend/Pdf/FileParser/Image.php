<?php

namespace Zend\Pdf\FileParser;


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
 * @subpackage FileParser
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  FileParser  */
require_once 'Zend/Pdf/FileParser.php';

/** \Zend\Log */
require_once 'Zend/Log.php';


/**
 * FileParser \for  PdfImage  subclasses.
 *
 * @package    \Zend\Pdf
 * @subpackage FileParser
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Pdf\FileParserDataSource as FileParserDataSource;
use Zend\Pdf\FileParser as FileParser;
use Zend\Pdf\Image as PdfImage;



abstract class  Image  extends  FileParser 
{
    protected $imageType;

    /**
     * Object constructor.
     *
     * Validates the data source and enables debug logging if so configured.
     *
     * @param  FileParserDataSource  $dataSource
     * @throws \Zend\Pdf\Exception
     */
    public function __construct( FileParserDataSource  $dataSource)
    {
        parent::__construct($dataSource);
       $this->imageType =  PdfImage ::TYPE_UNKNOWN;
    }

}


