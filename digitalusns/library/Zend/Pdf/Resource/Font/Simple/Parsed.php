<?php

namespace Zend\Pdf\Resource\Font\Simple;


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

/**  Simple  */
require_once 'Zend/Pdf/Resource/Font/Simple.php';

/**  OpenType  */
require_once 'Zend/Pdf/FileParser/Font/OpenType.php';


/**
 * Parsed and (optionaly) embedded fonts implementation
 *
 * OpenType fonts can contain either TrueType or PostScript Type 1 outlines.
 *
 * @package    \Zend\Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Pdf\FileParser\Font\OpenType as OpenType;
use Zend\Pdf\Resource\Font\Simple as Simple;
use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\Element\ElementArray as PdfElementArray;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Font as Font;



abstract class  Parsed  extends  Simple 
{
    /**
     * Object constructor
     *
     * @param  OpenType  $fontParser Font parser object containing OpenType file.
     * @throws \Zend\Pdf\Exception
     */
    public function __construct( OpenType  $fontParser)
    {
        parent::__construct();
    	
        
        $fontParser->parse();

        /* Object properties */

        $this->_fontNames = $fontParser->names;

        $this->_isBold       = $fontParser->isBold;
        $this->_isItalic     = $fontParser->isItalic;
        $this->_isMonospaced = $fontParser->isMonospaced;

        $this->_underlinePosition  = $fontParser->underlinePosition;
        $this->_underlineThickness = $fontParser->underlineThickness;
        $this->_strikePosition     = $fontParser->strikePosition;
        $this->_strikeThickness    = $fontParser->strikeThickness;

        $this->_unitsPerEm = $fontParser->unitsPerEm;

        $this->_ascent  = $fontParser->ascent;
        $this->_descent = $fontParser->descent;
        $this->_lineGap = $fontParser->lineGap;

        $this->_glyphWidths       = $fontParser->glyphWidths;
        $this->_missingGlyphWidth = $this->_glyphWidths[0];


        $this->_cmap = $fontParser->cmap;


        /* Resource dictionary */

        $baseFont = $this->getFontName( Font ::NAME_POSTSCRIPT, 'en', 'UTF-8');
        $this->_resource->BaseFont = new  Name ($baseFont);

        $this->_resource->FirstChar = new  Numeric (0);
        $this->_resource->LastChar  = new  Numeric (count($this->_glyphWidths) - 1);

        /* Now convert the scalar glyph widths to  Numeric  objects.
         */
        $pdfWidths = array();
        foreach ($this->_glyphWidths as $width) {
            $pdfWidths[] = new  Numeric ($this->toEmSpace($width));
        }
        /* Create the  PdfElementArray  object and add it to the font's
         * object factory and resource dictionary.
         */
        $widthsArrayElement = new  PdfElementArray ($pdfWidths);
        $widthsObject = $this->_objectFactory->newObject($widthsArrayElement);
        $this->_resource->Widths = $widthsObject;
    }

}
