<?php

namespace Zend\Pdf\Resource\Font\Simple\Parsed;


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
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Parsed  */
require_once 'Zend/Pdf/Resource/Font/Simple/Parsed.php';

/**  FontDescriptor  */
require_once 'Zend/Pdf/Resource/Font/FontDescriptor.php';



/**
 * TrueType fonts implementation
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link  Font ::fontWithName} and {@link  Font ::fontWithPath}.
 *
 * @package    \Zend\Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\FileParser\Font\OpenType\TrueType as PdfFileParserFontOpenTypeTrueType;
use Zend\Pdf\Resource\Font\FontDescriptor as FontDescriptor;
use Zend\Pdf\Resource\Font\Simple\Parsed as Parsed;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Font as Font;




class  TrueType  extends  Parsed 
{
    /**
     * Object constructor
     *
     * @param  PdfFileParserFontOpenTypeTrueType  $fontParser Font parser
     *   object containing parsed TrueType file.
     * @param integer $embeddingOptions Options \for font embedding.
     * @throws \Zend\Pdf\Exception
     */
    public function __construct( PdfFileParserFontOpenTypeTrueType  $fontParser, $embeddingOptions)
    {
        parent::__construct($fontParser, $embeddingOptions);

        $this->_fontType =  Font ::TYPE_TRUETYPE;

        $this->_resource->Subtype  = new  Name ('TrueType');

        $fontDescriptor =  FontDescriptor ::factory($this, $fontParser, $embeddingOptions);
        $this->_resource->FontDescriptor = $this->_objectFactory->newObject($fontDescriptor);
    }

}
