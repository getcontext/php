<?php

namespace Zend\Pdf;


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


/**  PdfException  */
require_once 'Zend/Pdf/Exception.php';

/**  Font  */
require_once 'Zend/Pdf/Resource/Font.php';

/**  Style  */
require_once 'Zend/Pdf/Style.php';

/**  Dictionary  */
require_once 'Zend/Pdf/Element/Dictionary.php';

/**  PdfElementReference  */
require_once 'Zend/Pdf/Element/Reference.php';

/**  ElementFactory  */
require_once 'Zend/Pdf/ElementFactory.php';

/**  Color  */
require_once 'Zend/Pdf/Color.php';

/**  Color _GrayScale */
require_once 'Zend/Pdf/Color/GrayScale.php';

/**  Color _Rgb */
require_once 'Zend/Pdf/Color/Rgb.php';

/**  Color _Cmyk */
require_once 'Zend/Pdf/Color/Cmyk.php';

/**
 * PDF \Page
 *
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\ElementFactory\ElementFactoryInterface as PdfElementFactoryInterface;
use Zend\Pdf\Resource\Font\Extracted as Extracted;
use Zend\Pdf\Element\Dictionary as Dictionary;
use Zend\Pdf\Element\Reference as PdfElementReference;
use Zend\Pdf\Element\LayoutBox as LayoutBox;
use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\ElementFactory as ElementFactory;
use Zend\Pdf\Element\ElementString as PdfElementString;
use Zend\Pdf\Element\Object as Object;
use Zend\Pdf\Resource\Image as PdfResourceImage;
use Zend\Pdf\Resource\Font as Font;
use Zend\Pdf\Element\ElementArray as PdfElementArray;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\Resource as Resource;
use Zend\Pdf\Element as Element;
use Zend\Pdf\Image as PdfImage;
use Zend\Pdf\Style as Style;
use Zend\Pdf\Color as Color;
use Zend\Pdf as Pdf;




class  Page 
{
  /**** Class Constants ****/


  /* \Page Sizes */

    /**
     * Size representing an A4 page in portrait (tall) orientation.
     */
    const SIZE_A4                = '595:842:';

    /**
     * Size representing an A4 page in landscape (wide) orientation.
     */
    const SIZE_A4_LANDSCAPE      = '842:595:';

    /**
     * Size representing a US Letter page in portrait (tall) orientation.
     */
    const SIZE_LETTER            = '612:792:';

    /**
     * Size representing a US Letter page in landscape (wide) orientation.
     */
    const SIZE_LETTER_LANDSCAPE  = '792:612:';


  /* Shape Drawing */

    /**
     * Stroke the path only. Do not fill.
     */
    const SHAPE_DRAW_STROKE      = 0;

    /**
     * Fill the path only. Do not stroke.
     */
    const SHAPE_DRAW_FILL        = 1;

    /**
     * Fill and stroke the path.
     */
    const SHAPE_DRAW_FILL_AND_STROKE = 2;


  /* Shape Filling Methods */

    /**
     * Fill the path using the non-zero winding rule.
     */
    const FILL_METHOD_NON_ZERO_WINDING = 0;

    /**
     * Fill the path using the even-odd rule.
     */
    const FILL_METHOD_EVEN_ODD        = 1;


  /* Line Dash Types */

    /**
     * Solid line dash.
     */
    const LINE_DASHING_SOLID = 0;



    /**
     * \Reference to the object with page dictionary.
     *
     * @var  PdfElementReference 
     */
    private $_pageDictionary;

    /**
     * PDF objects factory.
     *
     * @var  PdfElementFactoryInterface 
     */
    private $_objFactory = null;

    /**
     * Flag which signals, that page is created separately from any PDF document or
     * attached to anyone.
     *
     * @var boolean
     */
    private $_attached;

    /**
     * Stream \of the drawing instractions.
     *
     * @var string
     */
    private $_contents = '';

    /**
     * Current style
     *
     * @var  Style 
     */
    private $_style = null;

    /**
     * Counter \for the "Save" operations
     *
     * @var integer
     */
    private $_saveCount = 0;

    /**
     * Safe Graphics State semafore
     * 
     * If it's false, than we can't be sure Graphics State is restored withing 
     * context \of previous contents stream (ex. drawing coordinate system may be rotated).
     * We should encompass existing content with save/restore GS operators
     * 
     * @var boolean 
     */
    private $_safeGS;

    /**
     * Current font
     *
     * @var  Font 
     */
    private $_font = null;

    /**
     * Current font size
     *
     * @var float
     */
    private $_fontSize;

    /**
     * Object constructor.
     * Constructor signatures:
     *
     * 1. Load PDF page from a parsed PDF file.
     *    Object factory is created by PDF parser.
     * ---------------------------------------------------------
     * new  Page ( Dictionary        $pageDict,
     *                    PdfElementFactoryInterface  $factory);
     * ---------------------------------------------------------
     *
     * 2. Clone PDF page.
     *    New page is created in the same context as source page. Object factory is shared.
     *    Thus it will be attached to the document, but need to be placed into  Pdf ::$pages array
     *    to be included into output.
     * ---------------------------------------------------------
     * new  Page ( Pdf _Page $page);
     * ---------------------------------------------------------
     *
     * 3. Create new page with a specified pagesize.
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new  Page (string $pagesize,  PdfElementFactoryInterface  $factory = null);
     * ---------------------------------------------------------
     *
     * 4. Create new page with a specified pagesize (in default user space units).
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new  Page (numeric $width, numeric $height,  PdfElementFactoryInterface  $factory = null);
     * ---------------------------------------------------------
     *
     *
     * @param mixed $param1
     * @param mixed $param2
     * @param mixed $param3
     * @throws  PdfException 
     */
    public function __construct($param1, $param2 = null, $param3 = null)
    {
        if ($param1 instanceof  PdfElementReference  &&
            $param1->getType() ==  Element ::TYPE_DICTIONARY &&
            $param2 instanceof  PdfElementFactoryInterface  &&
            $param3 === null
           ) {
            $this->_pageDictionary = $param1;
            $this->_objFactory     = $param2;
            $this->_attached       = true;
            $this->_safeGS         = false;

            return;

        } else if ($param1 instanceof  Page  && $param2 === null && $param3 === null) {
            // Clone existing page.
            // Let already existing content and resources to be shared between pages
            // We don't give existing content modification functionality, so we don't need "deep copy"
            $this->_objFactory = $param1->_objFactory;
            $this->_attached   = &$param1->_attached;
            $this->_safeGS     = false;
            
            $this->_pageDictionary = $this->_objFactory->newObject(new  Dictionary ());

            foreach ($param1->_pageDictionary->getKeys() as $key) {
                if ($key == 'Contents') {
                    // Clone Contents property

                    $this->_pageDictionary->Contents = new  PdfElementArray ();

                    if ($param1->_pageDictionary->Contents->getType() !=  Element ::TYPE_ARRAY) {
                        // Prepare array \of content streams and add existing stream
                        $this->_pageDictionary->Contents->items[] = $param1->_pageDictionary->Contents;
                    } else {
                        // Clone array \of the content streams
                        foreach ($param1->_pageDictionary->Contents->items as $srcContentStream) {
                            $this->_pageDictionary->Contents->items[] = $srcContentStream;
                        }
                    }
                } else {
                    $this->_pageDictionary->$key = $param1->_pageDictionary->$key;
                }
            }

            return;
        } else if (is_string($param1) &&
                   ($param2 === null || $param2 instanceof  PdfElementFactoryInterface ) &&
                   $param3 === null) {
            $this->_objFactory = ($param2 !== null)? $param2 :  ElementFactory ::createFactory(1);
            $this->_attached   = false;
            $this->_safeGS     = true; /** New page created. That's users App responsibility to track GS changes */

            switch (strtolower($param1)) {
                case 'a4':
                    $param1 =  Page ::SIZE_A4;
                    break;
                case 'a4-landscape':
                    $param1 =  Page ::SIZE_A4_LANDSCAPE;
                    break;
                case 'letter':
                    $param1 =  Page ::SIZE_LETTER;
                    break;
                case 'letter-landscape':
                    $param1 =  Page ::SIZE_LETTER_LANDSCAPE;
                    break;
                default:
                    // should be in "x:y" form
            }

            $pageDim = explode(':', $param1);
            if(count($pageDim) == 3) {
                $pageWidth  = $pageDim[0];
                $pageHeight = $pageDim[1];
            } else {
                /**
                 * @todo support \of user defined pagesize notations, like:
                 *       "210x297mm", "595x842", "8.5x11in", "612x792"
                 */
                throw new  PdfException ('Wrong pagesize notation.');
            }
            /**
             * @todo support \of pagesize recalculation to "default user space units"
             */

        } else if (is_numeric($param1) && is_numeric($param2) &&
                   ($param3 === null || $param3 instanceof  PdfElementFactoryInterface )) {
            $this->_objFactory = ($param3 !== null)? $param3 :  ElementFactory ::createFactory(1);
            $this->_attached = false;
            $this->_safeGS   = true; /** New page created. That's users App responsibility to track GS changes */
            $pageWidth  = $param1;
            $pageHeight = $param2;

        } else {
            throw new  PdfException ('Unrecognized method signature, wrong number \of arguments or wrong argument types.');
        }

        $this->_pageDictionary = $this->_objFactory->newObject(new  Dictionary ());
        $this->_pageDictionary->Type         = new  Name ('\Page');
        $this->_pageDictionary->LastModified = new  PdfElementString ( Pdf ::pdfDate());
        $this->_pageDictionary->Resources    = new  Dictionary ();
        $this->_pageDictionary->MediaBox     = new  PdfElementArray ();
        $this->_pageDictionary->MediaBox->items[] = new  Numeric (0);
        $this->_pageDictionary->MediaBox->items[] = new  Numeric (0);
        $this->_pageDictionary->MediaBox->items[] = new  Numeric ($pageWidth);
        $this->_pageDictionary->MediaBox->items[] = new  Numeric ($pageHeight);
        $this->_pageDictionary->Contents     = new  PdfElementArray ();
    }


    /**
     * Clone operator
     *
     * @throws  PdfException 
     */
    public function __clone()
    {
        throw new  PdfException ('Cloning  Page  object using \'clone\' keyword is not supported. Use \'new  Page ($srcPage)\' syntax');
    }

    /**
     * Attach resource to the page
     *
     * @param string $type
     * @param  Resource  $resource
     * @return string
     */
    private function _attachResource($type,  Resource  $resource)
    {
        // Check that Resources dictionary contains appropriate resource set
        if ($this->_pageDictionary->Resources->$type === null) {
            $this->_pageDictionary->Resources->touch();
            $this->_pageDictionary->Resources->$type = new  Dictionary ();
        } else {
            $this->_pageDictionary->Resources->$type->touch();
        }

        // Check, that resource is already attached to resource set.
        $resObject = $resource->getResource();
        foreach ($this->_pageDictionary->Resources->$type->getKeys() as $ResID) {
            if ($this->_pageDictionary->Resources->$type->$ResID === $resObject) {
                return $ResID;
            }
        }

        $idCounter = 1;
        do {
            $newResName = $type[0] . $idCounter++;
        } while ($this->_pageDictionary->Resources->$type->$newResName !== null);

        $this->_pageDictionary->Resources->$type->$newResName = $resObject;
        $this->_objFactory->attach($resource->getFactory());

        return $newResName;
    }
    
    /**
     * Add procedureSet to the \Page description
     *
     * @param string $procSetName
     */
    private function _addProcSet($procSetName)
    {
        // Check that Resources dictionary contains ProcSet entry
        if ($this->_pageDictionary->Resources->ProcSet === null) {
            $this->_pageDictionary->Resources->touch();
            $this->_pageDictionary->Resources->ProcSet = new  PdfElementArray ();
        } else {
            $this->_pageDictionary->Resources->ProcSet->touch();
        }

        foreach ($this->_pageDictionary->Resources->ProcSet->items as $procSetEntry) {
            if ($procSetEntry->value == $procSetName) {
                // Procset is already included into a ProcSet array
                return;
            }
        }

        $this->_pageDictionary->Resources->ProcSet->items[] = new  Name ($procSetName);
    }

    /**
     * Retrive PDF file reference to the page
     *
     * @return  Dictionary 
     */
    public function getPageDictionary()
    {
        return $this->_pageDictionary;
    }

    /**
     * Dump current drawing instructions into the content stream.
     *
     * @todo Don't forget to close all current graphics operations (like path drawing)
     *
     * @throws  PdfException 
     */
    public function flush()
    {
        if ($this->_saveCount != 0) {
            throw new  PdfException ('Saved graphics state is not restored');
        }

        if ($this->_contents == '') {
            return;
        }

        if ($this->_pageDictionary->Contents->getType() !=  Element ::TYPE_ARRAY) {
            /**
             * It's a stream object.
             * Prepare Contents page attribute \for update.
             */
            $this->_pageDictionary->touch();

            $currentPageContents = $this->_pageDictionary->Contents;
            $this->_pageDictionary->Contents = new  PdfElementArray ();
            $this->_pageDictionary->Contents->items[] = $currentPageContents;
        } else {
            $this->_pageDictionary->Contents->touch();
        }

        if ((!$this->_safeGS)  &&  (count($this->_pageDictionary->Contents->items) != 0)) {
        	/**
        	 * \Page already has some content which is not treated as safe.
        	 * 
        	 * Add save/restore GS operators
        	 */
            $this->_addProcSet('PDF');
        	
        	$newContentsArray = new  PdfElementArray ();
        	$newContentsArray->items[] = $this->_objFactory->newStreamObject(" q\n");
        	foreach ($this->_pageDictionary->Contents->items as $contentStream) {
        		$newContentsArray->items[] = $contentStream;
        	}
            $newContentsArray->items[] = $this->_objFactory->newStreamObject(" Q\n");

        	$this->_pageDictionary->touch();
        	$this->_pageDictionary->Contents = $newContentsArray;
        	
        	$this->_safeGS = true;
        }
        
        $this->_pageDictionary->Contents->items[] =
                $this->_objFactory->newStreamObject($this->_contents);

        $this->_contents = '';
    }

    /**
     * Prepare page to be rendered into PDF.
     *
     * @todo Don't forget to close all current graphics operations (like path drawing)
     *
     * @param  PdfElementFactoryInterface  $objFactory
     * @throws  PdfException 
     */
    public function render( PdfElementFactoryInterface  $objFactory)
    {
        $this->flush();

        if ($objFactory === $this->_objFactory) {
            // \Page is already attached to the document.
            return;
        }

        if ($this->_attached) {
            throw new  PdfException ('\Page is attached to one documen, but rendered in context \of another.');
            /**
             * @todo \Page cloning must be implemented here instead \of exception.
             *       PDF objects (ex. fonts) can be shared between pages.
             *       Thus all referenced objects, which can be modified, must be cloned recursively,
             *       to avoid producing wrong object references in a context \of source PDF.
             */

            //...
        } else {
            $objFactory->attach($this->_objFactory);
        }

    }



    /**
     * Set fill color.
     *
     * @param  Color  $color
     */
    public function setFillColor( Color  $color)
    {
        $this->_addProcSet('PDF');
        $this->_contents .= $color->instructions(false);
    }

    /**
     * Set line color.
     *
     * @param  Color  $color
     */
    public function setLineColor( Color  $color)
    {
        $this->_addProcSet('PDF');
        $this->_contents .= $color->instructions(true);
    }

    /**
     * Set line width.
     *
     * @param float $width
     */
    public function setLineWidth($width)
    {
        $this->_addProcSet('PDF');
        $widthObj = new  Numeric ($width);
        $this->_contents .= $widthObj->toString() . " w\n";
    }

    /**
     * Set line dashing pattern
     *
     * Pattern is an array \of floats: array(on_length, off_length, on_length, off_length, ...)
     * Phase is shift from the beginning \of line.
     *
     * @param array $pattern
     * @param array $phase
     */
    public function setLineDashingPattern($pattern, $phase = 0)
    {
        $this->_addProcSet('PDF');

        if ($pattern ===  Page ::LINE_DASHING_SOLID) {
            $pattern = array();
            $phase   = 0;
        }

        $dashPattern  = new  PdfElementArray ();
        $phaseEleemnt = new  Numeric ($phase);

        foreach ($pattern as $dashItem) {
            $dashElement = new  Numeric ($dashItem);
            $dashPattern->items[] = $dashElement;
        }

        $this->_contents .= $dashPattern->toString() . ' '
                         . $phaseEleemnt->toString() . " d\n";
    }

    /**
     * Set current font.
     *
     * @param  Font  $font
     * @param float $fontSize
     */
    public function setFont( Font  $font, $fontSize)
    {
        $this->_addProcSet('Text');
        $fontName = $this->_attachResource('Font', $font);

        $this->_font     = $font;
        $this->_fontSize = $fontSize;

        $fontNameObj = new  Name ($fontName);
        $fontSizeObj = new  Numeric ($fontSize);
        $this->_contents .= $fontNameObj->toString() . ' ' . $fontSizeObj->toString() . " Tf\n";
    }

    /**
     * Set the style to use \for future drawing operations on this page
     *
     * @param  Style  $style
     */
    public function setStyle( Style  $style)
    {
        $this->_style = $style;

        $this->_addProcSet('Text');
        $this->_addProcSet('PDF');
        if ($style->getFont() !== null) {
            $this->setFont($style->getFont(), $style->getFontSize());
        }
        $this->_contents .= $style->instructions($this->_pageDictionary->Resources);
    }

    /**
     * Set the transparancy
     *
     * $alpha == 0  - transparent
     * $alpha == 1  - opaque
     *
     * Transparency modes, supported by PDF:
     * Normal (default), Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn, HardLight,
     * SoftLight, Difference, Exclusion
     *
     * @param float $alpha
     * @param string $mode
     * @throws  PdfException 
     */
    public function setAlpha($alpha, $mode = 'Normal')
    {
    	if (!in_array($mode, array('Normal', 'Multiply', 'Screen', 'Overlay', 'Darken', 'Lighten', 'ColorDodge',
    	                           'ColorBurn', 'HardLight', 'SoftLight', 'Difference', 'Exclusion'))) {
    	   throw new  PdfException ('Unsupported transparency mode.');
    	}
    	if (!is_numeric($alpha)  ||  $alpha < 0  ||  $alpha > 1) {
    		throw new  PdfException ('Alpha value must be numeric between 0 (transparent) and 1 (opaque).');
    	}

        $this->_addProcSet('Text');
        $this->_addProcSet('PDF');

        $resources = $this->_pageDictionary->Resources;

        // Check if Resources dictionary contains ExtGState entry
        if ($resources->ExtGState === null) {
            $resources->touch();
            $resources->ExtGState = new  Dictionary ();
        } else {
            $resources->ExtGState->touch();
        }

        $idCounter = 1;
        do {
            $gStateName = 'GS' . $idCounter++;
        } while ($resources->ExtGState->$gStateName !== null);


        $gStateDictionary = new  Dictionary ();
        $gStateDictionary->Type = new  Name ('ExtGState');
        $gStateDictionary->BM   = new  Name ($mode);
        $gStateDictionary->CA   = new  Numeric ($alpha);
        $gStateDictionary->ca   = new  Numeric ($alpha);

        $resources->ExtGState->$gStateName = $this->_objFactory->newObject($gStateDictionary);

        $gStateNameObj = new  Name ($gStateName);
        $this->_contents .= $gStateNameObj->toString() . " gs\n";
    }


    /**
     * Get current font.
     *
     * @return  Font  $font
     */
    public function getFont()
    {
        return $this->_font;
    }

    /**
     * Extract resources attached to the page
     *
     * This method is not intended to be used in userland, but helps to optimize some document wide operations
     * 
     * returns array \of  Dictionary  objects
     * 
     * @internal 
     * @return array
     */
    public function extractResources()
    {
        return $this->_pageDictionary->Resources;
    }
    
    /**
     * Extract fonts attached to the page
     *
     * returns array \of  Extracted  objects
     * 
     * @return array
     */
    public function extractFonts()
    {
        if ($this->_pageDictionary->Resources->Font === null) {
            // \Page doesn't have any font attached
            // Return empty array
            return array();
        }
        
        $fontResources = $this->_pageDictionary->Resources->Font;

        $fontResourcesUnique = array();
        foreach ($fontResources->getKeys() as $fontResourceName) {
            $fontDictionary = $fontResources->$fontResourceName;

            if (! ($fontDictionary instanceof  PdfElementReference   ||
                   $fontDictionary instanceof  Object ) ) {
                // Font dictionary has to be an indirect object or object reference
                continue;
            }

            $fontResourcesUnique[$fontDictionary->toString($this->_objFactory)] = $fontDictionary;
        }
        
        $fonts = array();
        foreach ($fontResourcesUnique as $resourceReference => $fontDictionary) {
            try {
                // Try to extract font
                $extractedFont = new  Extracted ($fontDictionary);

                $fonts[$resourceReference] = $extractedFont; 
            } catch ( PdfException  $e) {
                if ($e->getMessage() != 'Unsupported font type.') {
                    throw $e;
                }
            }
        }
        
        return $fonts;
    } 

    /**
     * Extract font attached to the page by specific font name
     * 
     * $fontName should be specified in UTF-8 encoding
     *
     * @return  Extracted |null
     */
    public function extractFont($fontName)
    {
        if ($this->_pageDictionary->Resources->Font === null) {
            // \Page doesn't have any font attached
            return null;
        }
        
        $fontResources = $this->_pageDictionary->Resources->Font;

        foreach ($fontResources->getKeys() as $fontResourceName) {
            $fontDictionary = $fontResources->$fontResourceName;
            
            if (! ($fontDictionary instanceof  PdfElementReference   ||
                   $fontDictionary instanceof  Object ) ) {
                // Font dictionary has to be an indirect object or object reference
                continue;
            }
            
            if ($fontDictionary->BaseFont->value != $fontName) {
                continue;
            }
            
            try {
                // Try to extract font
                return new  Extracted ($fontDictionary); 
            } catch ( PdfException  $e) {
                if ($e->getMessage() != 'Unsupported font type.') {
                    throw $e;
                }
                
                // Continue searhing font with specified name
            }
        }
        
        return null;
    } 

    /**
     * Get current font size
     *
     * @return float $fontSize
     */
    public function getFontSize()
    {
        return $this->_fontSize;
    }

    /**
     * Return the style, applied to the page.
     *
     * @return  Style |null
     */
    public function getStyle()
    {
        return $this->_style;
    }


    /**
     * Save the graphics state \of this page.
     * This takes a snapshot \of the currently applied style, position, clipping area and
     * any rotation/translation/scaling that has been applied.
     *
     * @todo check \for the open paths
     * @throws  PdfException     - if a save is performed with an open path
     */
    public function saveGS()
    {
        $this->_saveCount++;

        $this->_addProcSet('PDF');
        $this->_contents .= " q\n";
    }

    /**
     * Restore the graphics state that was saved with the last call to saveGS().
     *
     * @throws  PdfException    - if there is no previously saved state
     */
    public function restoreGS()
    {
        if ($this->_saveCount-- <= 0) {
            throw new  PdfException ('Restoring graphics state which is not saved');
        }
        $this->_contents .= " Q\n";
    }


    /**
     * Intersect current clipping area with a circle.
     *
     * @param float $x
     * @param float $y
     * @param float $radius
     * @param float $startAngle
     * @param float $endAngle
     */
    public function clipCircle($x, $y, $radius, $startAngle = null, $endAngle = null)
    {
        $this->clipEllipse($x - $radius, $y - $radius,
                           $x + $radius, $y + $radius,
                           $startAngle, $endAngle);
    }

    /**
     * Intersect current clipping area with a polygon.
     *
     * Method signatures:
     * drawEllipse($x1, $y1, $x2, $y2);
     * drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle);
     *
     * @todo process special cases with $x2-$x1 == 0 or $y2-$y1 == 0
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param float $startAngle
     * @param float $endAngle
     */
    public function clipEllipse($x1, $y1, $x2, $y2, $startAngle = null, $endAngle = null)
    {
        $this->_addProcSet('PDF');

        if ($x2 < $x1) {
            $temp = $x1;
            $x1   = $x2;
            $x2   = $temp;
        }
        if ($y2 < $y1) {
            $temp = $y1;
            $y1   = $y2;
            $y2   = $temp;
        }

        $x = ($x1 + $x2)/2.;
        $y = ($y1 + $y2)/2.;

        $xC = new  Numeric ($x);
        $yC = new  Numeric ($y);

        if ($startAngle !== null) {
            if ($startAngle != 0) { $startAngle = fmod($startAngle, M_PI*2); }
            if ($endAngle   != 0) { $endAngle   = fmod($endAngle,   M_PI*2); }

            if ($startAngle > $endAngle) {
                $endAngle += M_PI*2;
            }

            $clipPath    = $xC->toString() . ' ' . $yC->toString() . " m\n";
            $clipSectors = (int)ceil(($endAngle - $startAngle)/M_PI_4);
            $clipRadius  = max($x2 - $x1, $y2 - $y1);

            \for($count = 0; $count <= $clipSectors; $count++) {
                $pAngle = $startAngle + ($endAngle - $startAngle)*$count/(float)$clipSectors;

                $pX = new  Numeric ($x + cos($pAngle)*$clipRadius);
                $pY = new  Numeric ($y + sin($pAngle)*$clipRadius);
                $clipPath .= $pX->toString() . ' ' . $pY->toString() . " l\n";
            }

            $this->_contents .= $clipPath . "h\nW\nn\n";
        }

        $xLeft  = new  Numeric ($x1);
        $xRight = new  Numeric ($x2);
        $yUp    = new  Numeric ($y2);
        $yDown  = new  Numeric ($y1);

        $xDelta  = 2*(M_SQRT2 - 1)*($x2 - $x1)/3.;
        $yDelta  = 2*(M_SQRT2 - 1)*($y2 - $y1)/3.;
        $xr = new  Numeric ($x + $xDelta);
        $xl = new  Numeric ($x - $xDelta);
        $yu = new  Numeric ($y + $yDelta);
        $yd = new  Numeric ($y - $yDelta);

        $this->_contents .= $xC->toString() . ' ' . $yUp->toString() . " m\n"
                         .  $xr->toString() . ' ' . $yUp->toString() . ' '
                         .    $xRight->toString() . ' ' . $yu->toString() . ' '
                         .      $xRight->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xRight->toString() . ' ' . $yd->toString() . ' '
                         .    $xr->toString() . ' ' . $yDown->toString() . ' '
                         .      $xC->toString() . ' ' . $yDown->toString() . " c\n"
                         .  $xl->toString() . ' ' . $yDown->toString() . ' '
                         .    $xLeft->toString() . ' ' . $yd->toString() . ' '
                         .      $xLeft->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xLeft->toString() . ' ' . $yu->toString() . ' '
                         .    $xl->toString() . ' ' . $yUp->toString() . ' '
                         .      $xC->toString() . ' ' . $yUp->toString() . " c\n"
                         .  "h\nW\nn\n";
    }


    /**
     * Intersect current clipping area with a polygon.
     *
     * @param array $x  - array \of float (the X co-ordinates \of the vertices)
     * @param array $y  - array \of float (the Y co-ordinates \of the vertices)
     * @param integer $fillMethod
     */
    public function clipPolygon($x, $y, $fillMethod =  Page ::FILL_METHOD_NON_ZERO_WINDING)
    {
        $this->_addProcSet('PDF');

        $firstPoint = true;
        foreach ($x as $id => $xVal) {
            $xObj = new  Numeric ($xVal);
            $yObj = new  Numeric ($y[$id]);

            if ($firstPoint) {
                $path = $xObj->toString() . ' ' . $yObj->toString() . " m\n";
                $firstPoint = false;
            } else {
                $path .= $xObj->toString() . ' ' . $yObj->toString() . " l\n";
            }
        }

        $this->_contents .= $path;

        if ($fillMethod ==  Page ::FILL_METHOD_NON_ZERO_WINDING) {
            $this->_contents .= " h\n W\n";
        } else {
            // Even-Odd fill method.
            $this->_contents .= " h\n W*\n";
        }
    }

    /**
     * Intersect current clipping area with a rectangle.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     */
    public function clipRectangle($x1, $y1, $x2, $y2)
    {
        $this->_addProcSet('PDF');

        $x1Obj      = new  Numeric ($x1);
        $y1Obj      = new  Numeric ($y1);
        $widthObj   = new  Numeric ($x2 - $x1);
        $height2Obj = new  Numeric ($y2 - $y1);

        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                         .      $widthObj->toString() . ' ' . $height2Obj->toString() . " re\n"
                         .  " W\n";
    }

    /**
     * Draw a  Pdf _ContentStream at the specified position on the page
     *
     * @param ZPdfContentStream $cs
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     */
    public function drawContentStream($cs, $x1, $y1, $x2, $y2)
    {
    }

    /**
     * Draw a circle centered on x, y with a radius \of radius.
     *
     * Method signatures:
     * drawCircle($x, $y, $radius);
     * drawCircle($x, $y, $radius, $fillType);
     * drawCircle($x, $y, $radius, $startAngle, $endAngle);
     * drawCircle($x, $y, $radius, $startAngle, $endAngle, $fillType);
     *
     *
     * It's not a really circle, because PDF supports only cubic Bezier curves.
     * But _very_ good approximation.
     * It differs from a real circle on a maximum 0.00026 radiuses
     * (at PI/8, 3*PI/8, 5*PI/8, 7*PI/8, 9*PI/8, 11*PI/8, 13*PI/8 and 15*PI/8 angles).
     * At 0, PI/4, PI/2, 3*PI/4, PI, 5*PI/4, 3*PI/2 and 7*PI/4 it's exactly a tangent to a circle.
     *
     * @param float $x
     * @param float $y
     * @param float $radius
     * @param mixed $param4
     * @param mixed $param5
     * @param mixed $param6
     */
    public function  drawCircle($x, $y, $radius, $param4 = null, $param5 = null, $param6 = null)
    {
        $this->drawEllipse($x - $radius, $y - $radius,
                           $x + $radius, $y + $radius,
                           $param4, $param5, $param6);
    }

    /**
     * Draw an ellipse inside the specified rectangle.
     *
     * Method signatures:
     * drawEllipse($x1, $y1, $x2, $y2);
     * drawEllipse($x1, $y1, $x2, $y2, $fillType);
     * drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle);
     * drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle, $fillType);
     *
     * @todo process special cases with $x2-$x1 == 0 or $y2-$y1 == 0
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param mixed $param5
     * @param mixed $param6
     * @param mixed $param7
     */
    public function drawEllipse($x1, $y1, $x2, $y2, $param5 = null, $param6 = null, $param7 = null)
    {
        if ($param5 === null) {
            // drawEllipse($x1, $y1, $x2, $y2);
            $startAngle = null;
            $fillType =  Page ::SHAPE_DRAW_FILL_AND_STROKE;
        } else if ($param6 === null) {
            // drawEllipse($x1, $y1, $x2, $y2, $fillType);
            $startAngle = null;
            $fillType = $param5;
        } else {
            // drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle);
            // drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle, $fillType);
            $startAngle = $param5;
            $endAngle   = $param6;

            if ($param7 === null) {
                $fillType =  Page ::SHAPE_DRAW_FILL_AND_STROKE;
            } else {
                $fillType = $param7;
            }
        }

        $this->_addProcSet('PDF');

        if ($x2 < $x1) {
            $temp = $x1;
            $x1   = $x2;
            $x2   = $temp;
        }
        if ($y2 < $y1) {
            $temp = $y1;
            $y1   = $y2;
            $y2   = $temp;
        }

        $x = ($x1 + $x2)/2.;
        $y = ($y1 + $y2)/2.;

        $xC = new  Numeric ($x);
        $yC = new  Numeric ($y);

        if ($startAngle !== null) {
            if ($startAngle != 0) { $startAngle = fmod($startAngle, M_PI*2); }
            if ($endAngle   != 0) { $endAngle   = fmod($endAngle,   M_PI*2); }

            if ($startAngle > $endAngle) {
                $endAngle += M_PI*2;
            }

            $clipPath    = $xC->toString() . ' ' . $yC->toString() . " m\n";
            $clipSectors = (int)ceil(($endAngle - $startAngle)/M_PI_4);
            $clipRadius  = max($x2 - $x1, $y2 - $y1);

            \for($count = 0; $count <= $clipSectors; $count++) {
                $pAngle = $startAngle + ($endAngle - $startAngle)*$count/(float)$clipSectors;

                $pX = new  Numeric ($x + cos($pAngle)*$clipRadius);
                $pY = new  Numeric ($y + sin($pAngle)*$clipRadius);
                $clipPath .= $pX->toString() . ' ' . $pY->toString() . " l\n";
            }

            $this->_contents .= "q\n" . $clipPath . "h\nW\nn\n";
        }

        $xLeft  = new  Numeric ($x1);
        $xRight = new  Numeric ($x2);
        $yUp    = new  Numeric ($y2);
        $yDown  = new  Numeric ($y1);

        $xDelta  = 2*(M_SQRT2 - 1)*($x2 - $x1)/3.;
        $yDelta  = 2*(M_SQRT2 - 1)*($y2 - $y1)/3.;
        $xr = new  Numeric ($x + $xDelta);
        $xl = new  Numeric ($x - $xDelta);
        $yu = new  Numeric ($y + $yDelta);
        $yd = new  Numeric ($y - $yDelta);

        $this->_contents .= $xC->toString() . ' ' . $yUp->toString() . " m\n"
                         .  $xr->toString() . ' ' . $yUp->toString() . ' '
                         .    $xRight->toString() . ' ' . $yu->toString() . ' '
                         .      $xRight->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xRight->toString() . ' ' . $yd->toString() . ' '
                         .    $xr->toString() . ' ' . $yDown->toString() . ' '
                         .      $xC->toString() . ' ' . $yDown->toString() . " c\n"
                         .  $xl->toString() . ' ' . $yDown->toString() . ' '
                         .    $xLeft->toString() . ' ' . $yd->toString() . ' '
                         .      $xLeft->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xLeft->toString() . ' ' . $yu->toString() . ' '
                         .    $xl->toString() . ' ' . $yUp->toString() . ' '
                         .      $xC->toString() . ' ' . $yUp->toString() . " c\n";

        switch ($fillType) {
            case  Page ::SHAPE_DRAW_FILL_AND_STROKE:
                $this->_contents .= " B*\n";
                break;
            case  Page ::SHAPE_DRAW_FILL:
                $this->_contents .= " f*\n";
                break;
            case  Page ::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }

        if ($startAngle !== null) {
            $this->_contents .= "Q\n";
        }
    }

    /**
     * Draw an image at the specified position on the page.
     *
     * @param  PdfImage  $image
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     */
    public function drawImage( PdfResourceImage  $image, $x1, $y1, $x2, $y2)
    {
        $this->_addProcSet('PDF');

        $imageName    = $this->_attachResource('XObject', $image);
        $imageNameObj = new  Name ($imageName);

        $x1Obj     = new  Numeric ($x1);
        $y1Obj     = new  Numeric ($y1);
        $widthObj  = new  Numeric ($x2 - $x1);
        $heightObj = new  Numeric ($y2 - $y1);

        $this->_contents .= "q\n"
                         .  '1 0 0 1 ' . $x1Obj->toString() . ' ' . $y1Obj->toString() . " cm\n"
                         .  $widthObj->toString() . ' 0 0 ' . $heightObj->toString() . " 0 0 cm\n"
                         .  $imageNameObj->toString() . " Do\n"
                         .  "Q\n";
    }

    /**
     * Draw a LayoutBox at the specified position on the page.
     *
     * @param  LayoutBox  $box
     * @param float $x
     * @param float $y
     */
    public function drawLayoutBox($box, $x, $y)
    {
    }

    /**
     * Draw a line from x1,y1 to x2,y2.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     */
    public function drawLine($x1, $y1, $x2, $y2)
    {
        $this->_addProcSet('PDF');

        $x1Obj = new  Numeric ($x1);
        $y1Obj = new  Numeric ($y1);
        $x2Obj = new  Numeric ($x2);
        $y2Obj = new  Numeric ($y2);

        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " m\n"
                         .  $x2Obj->toString() . ' ' . $y2Obj->toString() . " l\n S\n";
    }

    /**
     * Draw a polygon.
     *
     * If $fillType is  Page ::SHAPE_DRAW_FILL_AND_STROKE or
     *  Page ::SHAPE_DRAW_FILL, then polygon is automatically closed.
     * See detailed description \of these methods in a PDF documentation
     * (section 4.4.2 Path painting Operators, Filling)
     *
     * @param array $x  - array \of float (the X co-ordinates \of the vertices)
     * @param array $y  - array \of float (the Y co-ordinates \of the vertices)
     * @param integer $fillType
     * @param integer $fillMethod
     */
    public function drawPolygon($x, $y,
                                $fillType =  Page ::SHAPE_DRAW_FILL_AND_STROKE,
                                $fillMethod =  Page ::FILL_METHOD_NON_ZERO_WINDING)
    {
        $this->_addProcSet('PDF');

        $firstPoint = true;
        foreach ($x as $id => $xVal) {
            $xObj = new  Numeric ($xVal);
            $yObj = new  Numeric ($y[$id]);

            if ($firstPoint) {
                $path = $xObj->toString() . ' ' . $yObj->toString() . " m\n";
                $firstPoint = false;
            } else {
                $path .= $xObj->toString() . ' ' . $yObj->toString() . " l\n";
            }
        }

        $this->_contents .= $path;

        switch ($fillType) {
            case  Page ::SHAPE_DRAW_FILL_AND_STROKE:
                if ($fillMethod ==  Page ::FILL_METHOD_NON_ZERO_WINDING) {
                    $this->_contents .= " b\n";
                } else {
                    // Even-Odd fill method.
                    $this->_contents .= " b*\n";
                }
                break;
            case  Page ::SHAPE_DRAW_FILL:
                if ($fillMethod ==  Page ::FILL_METHOD_NON_ZERO_WINDING) {
                    $this->_contents .= " h\n f\n";
                } else {
                    // Even-Odd fill method.
                    $this->_contents .= " h\n f*\n";
                }
                break;
            case  Page ::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }
    }

    /**
     * Draw a rectangle.
     *
     * Fill types:
     *  Page ::SHAPE_DRAW_FILL_AND_STROKE - fill rectangle and stroke (default)
     *  Page ::SHAPE_DRAW_STROKE      - stroke rectangle
     *  Page ::SHAPE_DRAW_FILL        - fill rectangle
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param integer $fillType
     */
    public function drawRectangle($x1, $y1, $x2, $y2, $fillType =  Page ::SHAPE_DRAW_FILL_AND_STROKE)
    {
        $this->_addProcSet('PDF');

        $x1Obj      = new  Numeric ($x1);
        $y1Obj      = new  Numeric ($y1);
        $widthObj   = new  Numeric ($x2 - $x1);
        $height2Obj = new  Numeric ($y2 - $y1);

        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                             .  $widthObj->toString() . ' ' . $height2Obj->toString() . " re\n";

        switch ($fillType) {
            case  Page ::SHAPE_DRAW_FILL_AND_STROKE:
                $this->_contents .= " B*\n";
                break;
            case  Page ::SHAPE_DRAW_FILL:
                $this->_contents .= " f*\n";
                break;
            case  Page ::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }
    }

    /**
     * Draw a line \of text at the specified position.
     *
     * @param string $text
     * @param float $x
     * @param float $y
     * @param string $charEncoding (optional) Character encoding \of source text.
     *   Defaults to current locale.
     * @throws  PdfException 
     */
    public function drawText($text, $x, $y, $charEncoding = '')
    {
        if ($this->_font === null) {
            throw new  PdfException ('Font has not been set');
        }

        $this->_addProcSet('Text');

        $textObj = new  PdfElementString ($this->_font->encodeString($text, $charEncoding));
        $xObj    = new  Numeric ($x);
        $yObj    = new  Numeric ($y);

        $this->_contents .= "BT\n"
                         .  $xObj->toString() . ' ' . $yObj->toString() . " Td\n"
                         .  $textObj->toString() . " Tj\n"
                         .  "ET\n";
    }

    /**
     * Return the height \of this page in points.
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->_pageDictionary->MediaBox->items[3]->value -
               $this->_pageDictionary->MediaBox->items[1]->value;
    }

    /**
     * Return the width \of this page in points.
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->_pageDictionary->MediaBox->items[2]->value -
               $this->_pageDictionary->MediaBox->items[0]->value;
    }

     /**
     * Close the path by drawing a straight line back to it's beginning.
     *
     * @throws  PdfException     - if a path hasn't been started with pathMove()
     */
    public function pathClose()
    {
    }

    /**
     * Continue the open path in a straight line to the specified position.
     *
     * @param float $x  - the X co-ordinate to move to
     * @param float $y  - the Y co-ordinate to move to
     */
    public function pathLine($x, $y)
    {
    }

    /**
     * Start a new path at the specified position. If a path has already been started,
     * move the cursor without drawing a line.
     *
     * @param float $x  - the X co-ordinate to move to
     * @param float $y  - the Y co-ordinate to move to
     */
    public function pathMove($x, $y)
    {
    }

    /**
     * Write raw PDF commands to the page.
     *
     * @param string $data
     */
    public function rawWrite($data)
    {
    }

    /**
     * Rotate the page.
     *
     * @param float $angle
     */
    public function rotate($x, $y, $angle)
    {
        $cos  = new  Numeric (cos($angle));
        $sin  = new  Numeric (sin($angle));
        $mSin = new  Numeric (-$sin->value);

        $xObj = new  Numeric ($x);
        $yObj = new  Numeric ($y);

        $mXObj = new  Numeric (-$x);
        $mYObj = new  Numeric (-$y);


        $this->_addProcSet('PDF');
        $this->_contents .= '1 0 0 1 ' . $xObj->toString() . ' ' . $yObj->toString() . " cm\n"
                         . $cos->toString() . ' ' . $sin->toString()
                         . ' ' . $mSin->toString() . ' ' . $cos->toString() . " 0 0 cm\n"
                         .'1 0 0 1 ' . $mXObj->toString() . ' ' . $mYObj->toString() . " cm\n";
    }


}

