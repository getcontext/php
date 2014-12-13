<?php

namespace Zend;


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
 * @category   Zend
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  PdfException  */
require_once 'Zend/Pdf/Exception.php';

/**  PdfPage  */
require_once 'Zend/Pdf/Page.php';

/**  Pdf _Cmap */
require_once 'Zend/Pdf/Cmap.php';

/**  Pdf _Font */
require_once 'Zend/Pdf/Font.php';

/**  Pdf _Style */
require_once 'Zend/Pdf/Style.php';

/**  Parser  */
require_once 'Zend/Pdf/Parser.php';

/**  Pdf _Trailer */
require_once 'Zend/Pdf/Trailer.php';

/**  Generator  */
require_once 'Zend/Pdf/Trailer/Generator.php';

/**  Pdf _Color */
require_once 'Zend/Pdf/Color.php';

/**  Pdf _Color_GrayScale */
require_once 'Zend/Pdf/Color/GrayScale.php';

/**  Pdf _Color_Rgb */
require_once 'Zend/Pdf/Color/Rgb.php';

/**  Pdf _Color_Cmyk */
require_once 'Zend/Pdf/Color/Cmyk.php';

/**  Pdf _Color_Html */
require_once 'Zend/Pdf/Color/Html.php';

/**  Pdf _Image */
require_once 'Zend/Pdf/Resource/Image.php';

/**  Pdf _Image */
require_once 'Zend/Pdf/Image.php';

/**  Pdf _Image_Jpeg */
require_once 'Zend/Pdf/Resource/Image/Jpeg.php';

/**  Pdf _Image_Tiff */
require_once 'Zend/Pdf/Resource/Image/Tiff.php';

/**  Pdf _Image_Png */
require_once 'Zend/Pdf/Resource/Image/Png.php';


/**  Memory  */
require_once 'Zend/Memory.php';


/**
 * General entity which describes PDF document.
 * It implements document abstraction with a document level operations.
 *
 * Class is used to create new PDF document or load existing document.
 * See details in a class constructor description
 *
 * Class agregates document level properties and entities (pages, bookmarks,
 * document level actions, attachments, form object, etc)
 *
 * @category   Zend
 * @package     Pdf 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Resource\Font\Extracted as Extracted;
use Zend\Pdf\Element\String\Binary as Binary;
use Zend\Pdf\Element\Dictionary as Dictionary;
use Zend\Pdf\Trailer\Generator as Generator;
use Zend\Pdf\Element\Reference as PdfElementReference;
use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\Element\ElementString as PdfElementString;
use Zend\Pdf\ElementFactory as ElementFactory;
use Zend\Pdf\Element\Object as Object;
use Zend\Pdf\Element\ElementArray as PdfElementArray;
use Zend\Pdf\Element\Name as Name;
use Zend\Memory\Manager as Manager;
use Zend\Pdf\Exception as PdfException;
use Zend\Pdf\Element as Element;
use Zend\Pdf\Parser as Parser;
use Zend\Pdf\Page as PdfPage;
use Zend\Memory as Memory;




class  Pdf 
{
  /**** Class Constants ****/

    /**
     * Version number \of generated PDF documents.
     */
    const PDF_VERSION = 1.4;

    /**
     * PDF file header.
     */
    const PDF_HEADER  = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";



    /**
     * Pages collection
     *
     * @todo implement it as a class, which supports \ArrayAccess and \Iterator interfaces,
     *       to provide incremental parsing and pages tree updating.
     *       That will give good performance and memory (PDF size) benefits.
     *
     * @var array   - array \of  PdfPage  object
     */
    public $pages = array();

    /**
     * Document properties
     *
     * It's an associative array with PDF meta information, values may
     * be string, boolean or float.
     * Returned array could be used directly to access, add, modify or remove
     * document properties.
     *
     * Standard document properties: Title (must be set \for PDF/X documents), Author,
     * Subject, Keywords (comma separated list), Creator (the name \of the application,
     * that created document, if it was converted from other format), Trapped (must be
     * true, false or null, can not be null \for PDF/X documents)
     *
     * @var array
     */
    public $properties = array();

    /**
     * Original properties set.
     *
     * Used \for tracking properties changes
     *
     * @var array
     */
    private $_originalProperties = array();

    /**
     * Document level javascript
     *
     * @var string
     */
    private $_javaScript = null;

    /**
     * Document named actions
     * "GoTo..." actions, used to refer document parts
     * from outside PDF
     *
     * @var array   - array \of  Pdf _Action objects
     */
    private $_namedActions = array();


    /**
     * Pdf trailer (last or just created)
     *
     * @var  Pdf _Trailer
     */
    private $_trailer = null;


    /**
     * PDF objects factory.
     *
     * @var  ElementFactory _Interface
     */
    private $_objFactory = null;

    /**
     * Memory manager \for stream objects
     *
     * @var  Manager |null
     */
    private static $_memoryManager = null;

    /**
     * Pdf file parser.
     * It's not used, but has to be destroyed only with  Pdf  object
     *
     * @var  Parser 
     */
    private $_parser;

    /**
     * Request used memory manager
     *
     * @return  Manager 
     */
    static public function getMemoryManager()
    {
        if (self::$_memoryManager === null) {
            self::$_memoryManager =  Memory ::factory('none');
        }

        return self::$_memoryManager;
    }

    /**
     * Set user defined memory manager
     *
     * @param  Manager  $memoryManager
     */
    static public function setMemoryManager( Manager  $memoryManager)
    {
        self::$_memoryManager = $memoryManager;
    }


    /**
     * Create new PDF document from a $source string
     *
     * @param string $source
     * @param integer $revision
     * @return  Pdf 
     */
    public static function parse(&$source = null, $revision = null)
    {
        return new  Pdf ($source, $revision);
    }

    /**
     * Load PDF document from a file
     *
     * @param string $source
     * @param integer $revision
     * @return  Pdf 
     */
    public static function load($source = null, $revision = null)
    {
        return new  Pdf ($source, $revision, true);
    }

    /**
     * Render PDF document and save it.
     *
     * If $updateOnly is true, then it only appends new section to the end \of file.
     *
     * @param string $filename
     * @param boolean $updateOnly
     * @throws  PdfException 
     */
    public function save($filename, $updateOnly = false)
    {
        if (($file = @fopen($filename, $updateOnly ? 'ab':'wb')) === false ) {
            throw new  PdfException ( "Can not open '$filename' file \for writing." );
        }

        $this->render($updateOnly, $file);

        fclose($file);
    }

    /**
     * Creates or loads PDF document.
     *
     * If $source is null, then it creates a new document.
     *
     * If $source is a string and $load is false, then it loads document
     * from a binary string.
     *
     * If $source is a string and $load is true, then it loads document
     * from a file.

     * $revision used to roll back document to specified version
     * (0 - currtent version, 1 - previous version, 2 - ...)
     *
     * @param string  $source - PDF file to load
     * @param integer $revision
     * @throws  PdfException 
     * @return  Pdf 
     */
    public function __construct($source = null, $revision = null, $load = false)
    {
        $this->_objFactory =  ElementFactory ::createFactory(1);

        if ($source !== null) {
            $this->_parser  = new  Parser ($source, $this->_objFactory, $load);
            $this->_trailer = $this->_parser->getTrailer();
            if ($revision !== null) {
                $this->rollback($revision);
            } else {
                $this->_loadPages($this->_trailer->Root->Pages);
            }

            if ($this->_trailer->Info !== null) {
                foreach ($this->_trailer->Info->getKeys() as $key) {
                    $this->properties[$key] = $this->_trailer->Info->$key->value;
                }

                if (isset($this->properties['Trapped'])) {
                    switch ($this->properties['Trapped']) {
                        case 'True':
                            $this->properties['Trapped'] = true;
                            break;

                        case 'False':
                            $this->properties['Trapped'] = false;
                            break;

                        case 'Unknown':
                            $this->properties['Trapped'] = null;
                            break;

                        default:
                            // Wrong property value
                            // Do nothing
                            break;
                    }
                }

                $this->_originalProperties = $this->properties;
            }
        } else {
            $trailerDictionary = new  Dictionary ();

            /**
             * Document id
             */
            $docId = md5(uniqid(rand(), true));   // 32 byte (128 bit) identifier
            $docIdLow  = substr($docId,  0, 16);  // first 16 bytes
            $docIdHigh = substr($docId, 16, 16);  // second 16 bytes

            $trailerDictionary->ID = new  PdfElementArray ();
            $trailerDictionary->ID->items[] = new  Binary ($docIdLow);
            $trailerDictionary->ID->items[] = new  Binary ($docIdHigh);

            $trailerDictionary->Size = new  Numeric (0);

            $this->_trailer    = new  Generator ($trailerDictionary);

            /**
             * Document catalog indirect object.
             */
            $docCatalog = $this->_objFactory->newObject(new  Dictionary ());
            $docCatalog->Type    = new  Name ('Catalog');
            $docCatalog->Version = new  Name ( Pdf ::PDF_VERSION);
            $this->_trailer->Root = $docCatalog;

            /**
             * Pages container
             */
            $docPages = $this->_objFactory->newObject(new  Dictionary ());
            $docPages->Type  = new  Name ('Pages');
            $docPages->Kids  = new  PdfElementArray ();
            $docPages->Count = new  Numeric (0);
            $docCatalog->Pages = $docPages;
        }
    }

    /**
     * Retrive number \of revisions.
     *
     * @return integer
     */
    public function revisions()
    {
        $revisions = 1;
        $currentTrailer = $this->_trailer;

        while ($currentTrailer->getPrev() !== null && $currentTrailer->getPrev()->Root !== null ) {
            $revisions++;
            $currentTrailer = $currentTrailer->getPrev();
        }

        return $revisions++;
    }

    /**
     * Rollback document $steps number \of revisions.
     * This method must be invoked before any changes, applied to the document.
     * Otherwise behavior is undefined.
     *
     * @param integer $steps
     */
    public function rollback($steps)
    {
        \for ($count = 0; $count < $steps; $count++) {
            if ($this->_trailer->getPrev() !== null && $this->_trailer->getPrev()->Root !== null) {
                $this->_trailer = $this->_trailer->getPrev();
            } else {
                break;
            }
        }
        $this->_objFactory->setObjectCount($this->_trailer->Size->value);

        // Mark content as modified to force new trailer generation at render time
        $this->_trailer->Root->touch();

        $this->pages = array();
        $this->_loadPages($this->_trailer->Root->Pages);
    }



    /**
     * List \of inheritable attributesfor pages tree
     *
     * @var array
     */
    private static $_inheritableAttributes = array('Resources', 'MediaBox', 'CropBox', 'Rotate');


    /**
     * Load pages recursively
     *
     * @param  PdfElementReference  $pages
     * @param array|null $attributes
     */
    private function _loadPages( PdfElementReference  $pages, $attributes = array())
    {
        if ($pages->getType() !=  Element ::TYPE_DICTIONARY) {
            throw new  PdfException ('Wrong argument');
        }

        foreach ($pages->getKeys() as $property) {
            if (in_array($property, self::$_inheritableAttributes)) {
                $attributes[$property] = $pages->$property;
                $pages->$property = null;
            }
        }


        foreach ($pages->Kids->items as $child) {
            if ($child->Type->value == 'Pages') {
                $this->_loadPages($child, $attributes);
            } else if ($child->Type->value == '\Page') {
                foreach (self::$_inheritableAttributes as $property) {
                    if ($child->$property === null && array_key_exists($property, $attributes)) {
                        /**
                         * Important note.
                         * If any attribute or dependant object is an indirect object, then it's still
                         * shared between pages.
                         */
                        if ($attributes[$property] instanceof  Object ) {
                            $child->$property = $attributes[$property];
                        } else {
                            $child->$property = $this->_objFactory->newObject($attributes[$property]);
                        }
                    }
                }
                $this->pages[] = new  PdfPage ($child, $this->_objFactory);
            }
        }
    }


    /**
     * Orginize pages to tha pages tree structure.
     *
     * @todo atomatically attach page to the document, if it's not done yet.
     * @todo check, that page is attached to the current document
     *
     * @todo Dump pages as a balanced tree instead \of a plain set.
     */
    private function _dumpPages()
    {
        $pagesContainer = $this->_trailer->Root->Pages;
        $pagesContainer->touch();
        $pagesContainer->Kids->items->clear();

        foreach ($this->pages as $page ) {
            $page->render($this->_objFactory);

            $pageDictionary = $page->getPageDictionary();
            $pageDictionary->touch();
            $pageDictionary->Parent = $pagesContainer;

            $pagesContainer->Kids->items[] = $pageDictionary;
        }

        $pagesContainer->Count->touch();
        $pagesContainer->Count->value = count($this->pages);
    }


    /**
     * Create page object, attached to the PDF document.
     * Method signatures:
     *
     * 1. Create new page with a specified pagesize.
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new  PdfPage (string $pagesize);
     * ---------------------------------------------------------
     *
     * 2. Create new page with a specified pagesize (in default user space units).
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new  PdfPage (numeric $width, numeric $height);
     * ---------------------------------------------------------
     *
     * @param mixed $param1
     * @param mixed $param2
     * @return  PdfPage 
     */
    public function newPage($param1, $param2 = null)
    {
        if ($param2 === null) {
            return new  PdfPage ($param1, $this->_objFactory);
        } else {
            return new  PdfPage ($param1, $param2, $this->_objFactory);
        }
    }

    /**
     * Return the document-level Metadata
     * or null Metadata stream is not presented
     *
     * @return string
     */
    public function getMetadata()
    {
        if ($this->_trailer->Root->Metadata !== null) {
            return $this->_trailer->Root->Metadata->value;
        } else {
            return null;
        }
    }

    /**
     * Sets the document-level Metadata (mast be valid XMP document)
     *
     * @param string $metadata
     */
    public function setMetadata($metadata)
    {
        $metadataObject = $this->_objFactory->newStreamObject($metadata);
        $metadataObject->dictionary->Type    = new  Name ('Metadata');
        $metadataObject->dictionary->Subtype = new  Name ('XML');

        $this->_trailer->Root->Metadata = $metadataObject;
        $this->_trailer->Root->touch();
    }

    /**
     * Return the document-level JavaScript
     * or null if there is no JavaScript \for this document
     *
     * @return string
     */
    public function getJavaScript()
    {
        return $this->_javaScript;
    }


    /**
     * Return an associative array containing all the named actions in the PDF.
     * Named actions (it's always "GoTo" actions) can be used to reference from outside
     * the PDF, ex: 'http://www.something.com/mydocument.pdf#MyAction'
     *
     * @return array
     */
    public function getNamedActions()
    {
        return $this->_namedActions;
    }

    /**
     * Extract fonts attached to the document
     *
     * returns array \of  Extracted  objects
     * 
     * @return array
     */
    public function extractFonts()
    {
        $fontResourcesUnique = array();
        foreach ($this->pages as $page) {
            $pageResources = $page->extractResources();

            if ($pageResources->Font === null) {
                // \Page doesn't contain have any font reference
                continue;
            }
            
            $fontResources = $pageResources->Font;

            foreach ($fontResources->getKeys() as $fontResourceName) {
                $fontDictionary = $fontResources->$fontResourceName;
    
                if (! ($fontDictionary instanceof  PdfElementReference   ||
                       $fontDictionary instanceof  Object ) ) {
                    // Font dictionary has to be an indirect object or object reference
                    continue;
                }
    
                $fontResourcesUnique[$fontDictionary->toString($this->_objFactory)] = $fontDictionary;
            }
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
        $fontResourcesUnique = array();
        foreach ($this->pages as $page) {
            $pageResources = $page->extractResources();
            
            if ($pageResources->Font === null) {
                // \Page doesn't contain have any font reference
                continue;
            }
            
            $fontResources = $pageResources->Font;

            foreach ($fontResources->getKeys() as $fontResourceName) {
                $fontDictionary = $fontResources->$fontResourceName;
    
                if (! ($fontDictionary instanceof  PdfElementReference   ||
                       $fontDictionary instanceof  Object ) ) {
                    // Font dictionary has to be an indirect object or object reference
                    continue;
                }
                
                $resourceReference = $fontDictionary->toString($this->_objFactory);
                if (isset($fontResourcesUnique[$resourceReference])) {
                    continue;
                } else {
                    // Mark resource as processed
                    $fontResourcesUnique[$resourceReference] = 1;
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
                    // Continue searhing
                }
            }
        }

        return null;
    } 
    
    /**
     * Render the completed PDF to a string.
     * If $newSegmentOnly is true, then only appended part \of PDF is returned.
     *
     * @param boolean $newSegmentOnly
     * @param resource $outputStream
     * @return string
     * @throws  PdfException 
     */
    public function render($newSegmentOnly = false, $outputStream = null)
    {
        // Save document properties if necessary
        if ($this->properties != $this->_originalProperties) {
            $docInfo = $this->_objFactory->newObject(new  Dictionary ());

            foreach ($this->properties as $key => $value) {
                switch ($key) {
                    case 'Trapped':
                        switch ($value) {
                            case true:
                                $docInfo->$key = new  Name ('True');
                                break;

                            case false:
                                $docInfo->$key = new  Name ('False');
                                break;

                            case null:
                                $docInfo->$key = new  Name ('Unknown');
                                break;

                            default:
                                throw new  PdfException ('Wrong Trapped document property vale: \'' . $value . '\'. Only true, false and null values are allowed.');
                                break;
                        }

                    case 'CreationDate':
                        // break intentionally omitted
                    case 'ModDate':
                        $docInfo->$key = new  PdfElementString ((string)$value);
                        break;

                    case 'Title':
                        // break intentionally omitted
                    case 'Author':
                        // break intentionally omitted
                    case 'Subject':
                        // break intentionally omitted
                    case 'Keywords':
                        // break intentionally omitted
                    case 'Creator':
                        // break intentionally omitted
                    case 'Producer':
                        // break intentionally omitted
                    default:
                        $docInfo->$key = new  PdfElementString ((string)$value);
                        break;
                }
            }

            $this->_trailer->Info = $docInfo;
        }

        $this->_dumpPages();

        // Check, that PDF file was modified
        // File is always modified by _dumpPages() now, but future implementations may eliminate this.
        if (!$this->_objFactory->isModified()) {
            if ($newSegmentOnly) {
                // Do nothing, return
                return '';
            }

            if ($outputStream === null) {
                return $this->_trailer->getPDFString();
            } else {
                $pdfData = $this->_trailer->getPDFString();
                while ( strlen($pdfData) > 0 && ($byteCount = fwrite($outputStream, $pdfData)) != false ) {
                    $pdfData = substr($pdfData, $byteCount);
                }

                return '';
            }
        }

        // offset (from a start \of PDF file) \of new PDF file segment
        $offset = $this->_trailer->getPDFLength();
        // Last Object number in a list \of free objects
        $lastFreeObject = $this->_trailer->getLastFreeObject();

        // Array \of cross-reference table subsections
        $xrefTable = array();
        // Object numbers \of first objects in each subsection
        $xrefSectionStartNums = array();

        // Last cross-reference table subsection
        $xrefSection = array();
        // Dummy initialization \of the first element (specail case - header \of linked list \of free objects).
        $xrefSection[] = 0;
        $xrefSectionStartNums[] = 0;
        // Object number \of last processed PDF object.
        // Used to manage cross-reference subsections.
        // Initialized by zero (specail case - header \of linked list \of free objects).
        $lastObjNum = 0;

        if ($outputStream !== null) {
            if (!$newSegmentOnly) {
                $pdfData = $this->_trailer->getPDFString();
                while ( strlen($pdfData) > 0 && ($byteCount = fwrite($outputStream, $pdfData)) != false ) {
                    $pdfData = substr($pdfData, $byteCount);
                }
            }
        } else {
            $pdfSegmentBlocks = ($newSegmentOnly) ? array() : array($this->_trailer->getPDFString());
        }

        // Iterate objects to create new reference table
        foreach ($this->_objFactory->listModifiedObjects() as $updateInfo) {
            $objNum = $updateInfo->getObjNum();

            if ($objNum - $lastObjNum != 1) {
                // Save cross-reference table subsection and start new one
                $xrefTable[] = $xrefSection;
                $xrefSection = array();
                $xrefSectionStartNums[] = $objNum;
            }

            if ($updateInfo->isFree()) {
                // Free object cross-reference table entry
                $xrefSection[]  = sprintf("%010d %05d f \n", $lastFreeObject, $updateInfo->getGenNum());
                $lastFreeObject = $objNum;
            } else {
                // In-use object cross-reference table entry
                $xrefSection[]  = sprintf("%010d %05d n \n", $offset, $updateInfo->getGenNum());

                $pdfBlock = $updateInfo->getObjectDump();
                $offset += strlen($pdfBlock);

                if ($outputStream === null) {
                    $pdfSegmentBlocks[] = $pdfBlock;
                } else {
                    while ( strlen($pdfBlock) > 0 && ($byteCount = fwrite($outputStream, $pdfBlock)) != false ) {
                        $pdfBlock = substr($pdfBlock, $byteCount);
                    }
                }
            }
            $lastObjNum = $objNum;
        }
        // Save last cross-reference table subsection
        $xrefTable[] = $xrefSection;

        // Modify first entry (specail case - header \of linked list \of free objects).
        $xrefTable[0][0] = sprintf("%010d 65535 f \n", $lastFreeObject);

        $xrefTableStr = "xref\n";
        foreach ($xrefTable as $sectId => $xrefSection) {
            $xrefTableStr .= sprintf("%d %d \n", $xrefSectionStartNums[$sectId], count($xrefSection));
            foreach ($xrefSection as $xrefTableEntry) {
                $xrefTableStr .= $xrefTableEntry;
            }
        }

        $this->_trailer->Size->value = $this->_objFactory->getObjectCount();

        $pdfBlock = $xrefTableStr
                 .  $this->_trailer->toString()
                 . "startxref\n" . $offset . "\n"
                 . "%%EOF\n";

        if ($outputStream === null) {
            $pdfSegmentBlocks[] = $pdfBlock;

            return implode('', $pdfSegmentBlocks);
        } else {
            while ( strlen($pdfBlock) > 0 && ($byteCount = fwrite($outputStream, $pdfBlock)) != false ) {
                $pdfBlock = substr($pdfBlock, $byteCount);
            }

            return '';
        }
    }


    /**
     * Set the document-level JavaScript
     *
     * @param string $javascript
     */
    public function setJavaScript($javascript)
    {
        $this->_javaScript = $javascript;
    }


    /**
     * Convert date to PDF format (it's close to ASN.1 (Abstract Syntax Notation
     * One) defined in ISO/IEC 8824).
     *
     * @todo This really isn't the best location \for this method. It should
     *   probably actually exist as  Element _Date or something like that.
     *
     * @todo Address the following E_STRICT issue:
     *   PHP Strict Standards:  date(): It is not safe to rely on the system's
     *   timezone settings. Please use the date.timezone setting, the TZ
     *   environment variable or the date_default_timezone_set() function. In
     *   case you used any \of those methods and you are still getting this
     *   warning, you most likely misspelled the timezone identifier.
     *
     * @param integer $timestamp (optional) If omitted, uses the current time.
     * @return string
     */
    public static function pdfDate($timestamp = null)
    {
        if (is_null($timestamp)) {
            $date = date('\D\:YmdHisO');
        } else {
            $date = date('\D\:YmdHisO', $timestamp);
        }
        return substr_replace($date, '\'', -2, 0) . '\'';
    }

}
