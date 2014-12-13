<?php

namespace Zend\Pdf\Resource\Image;


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


/**  Image  */
require_once 'Zend/Pdf/Resource/Image.php';

/**  PdfException  */
require_once 'Zend/Pdf/Exception.php';

/**  Numeric  */
require_once 'Zend/Pdf/Element/Numeric.php';

/**  Name  */
require_once 'Zend/Pdf/Element/Name.php';


/**
 * TIFF image
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\Resource\Image as Image;
use Zend\Pdf\Element\ElementArray as PdfElementArray;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Exception as PdfException;




class  Tiff  extends  Image 
{
    const TIFF_FIELD_TYPE_BYTE=1;
    const TIFF_FIELD_TYPE_ASCII=2;
    const TIFF_FIELD_TYPE_SHORT=3;
    const TIFF_FIELD_TYPE_LONG=4;
    const TIFF_FIELD_TYPE_RATIONAL=5;

    const TIFF_TAG_IMAGE_WIDTH=256;
    const TIFF_TAG_IMAGE_LENGTH=257; //Height
    const TIFF_TAG_BITS_PER_SAMPLE=258;
    const TIFF_TAG_COMPRESSION=259;
    const TIFF_TAG_PHOTOMETRIC_INTERPRETATION=262;
    const TIFF_TAG_STRIP_OFFSETS=273;
    const TIFF_TAG_SAMPLES_PER_PIXEL=277;
    const TIFF_TAG_STRIP_BYTE_COUNTS=279;

    const TIFF_COMPRESSION_UNCOMPRESSED = 1;
    const TIFF_COMPRESSION_CCITT1D = 2;
    const TIFF_COMPRESSION_GROUP_3_FAX = 3;
    const TIFF_COMPRESSION_GROUP_4_FAX  = 4;
    const TIFF_COMPRESSION_LZW = 5;
    const TIFF_COMPRESSION_JPEG = 6;
    const TIFF_COMPRESSION_FLATE = 8;
    const TIFF_COMPRESSION_FLATE_OBSOLETE_CODE = 32946;
    const TIFF_COMPRESSION_PACKBITS = 32773;

    const TIFF_PHOTOMETRIC_INTERPRETATION_WHITE_IS_ZERO=0;
    const TIFF_PHOTOMETRIC_INTERPRETATION_BLACK_IS_ZERO=1;
    const TIFF_PHOTOMETRIC_INTERPRETATION_RGB=2;
    const TIFF_PHOTOMETRIC_INTERPRETATION_RGB_INDEXED=3;
    const TIFF_PHOTOMETRIC_INTERPRETATION_CMYK=5;
    const TIFF_PHOTOMETRIC_INTERPRETATION_YCBCR=6;
    const TIFF_PHOTOMETRIC_INTERPRETATION_CIELAB=8;

    protected $_width;
    protected $_height;
    protected $_imageProperties;
    protected $_endianType;
    protected $_fileSize;
    protected $_bitsPerSample;
    protected $_compression;
    protected $_filter;
    protected $_colorCode;
    protected $_whiteIsZero;
    protected $_blackIsZero;
    protected $_colorSpace;
    protected $_imageDataOffset;
    protected $_imageDataLength;

    const TIFF_ENDIAN_BIG=0;
    const TIFF_ENDIAN_LITTLE=1;

    const UNPACK_TYPE_BYTE=0;
    const UNPACK_TYPE_SHORT=1;
    const UNPACK_TYPE_LONG=2;
    const UNPACK_TYPE_RATIONAL=3;

    /**
     * Byte unpacking function
     *
     * Makes it possible to unpack bytes in one statement \for enhanced logic readability.
     *
     * @param int $type
     * @param string $bytes
     * @throws  PdfException 
     */
    protected function unpackBytes($type, $bytes) {
        if(!isset($this->_endianType)) {
            throw new  PdfException ("The unpackBytes function can only be used after the endianness \of the file is known");
        }
        switch($type) {
            case  Tiff ::UNPACK_TYPE_BYTE:
                $format = 'C';
                $unpacked = unpack($format, $bytes);
                return $unpacked[1];
                break;
            case  Tiff ::UNPACK_TYPE_SHORT:
                $format = ($this->_endianType ==  Tiff ::TIFF_ENDIAN_LITTLE)?'v':'n';
                $unpacked = unpack($format, $bytes);
                return $unpacked[1];
                break;
            case  Tiff ::UNPACK_TYPE_LONG:
                $format = ($this->_endianType ==  Tiff ::TIFF_ENDIAN_LITTLE)?'V':'N';
                $unpacked = unpack($format, $bytes);
                return $unpacked[1];
                break;
            case  Tiff ::UNPACK_TYPE_RATIONAL:
                $format = ($this->_endianType ==  Tiff ::TIFF_ENDIAN_LITTLE)?'V2':'N2';
                $unpacked = unpack($format, $bytes);
                return ($unpacked[1]/$unpacked[2]);
                break;
        }
    }

    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws  PdfException 
     */
    public function __construct($imageFileName)
    {
        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new  PdfException ( "Can not open '$imageFileName' file \for reading." );
        }

        $byteOrderIndicator = fread($imageFile, 2);
        if($byteOrderIndicator == 'II') {
            $this->_endianType =  Tiff ::TIFF_ENDIAN_LITTLE;
        } else if($byteOrderIndicator == 'MM') {
            $this->_endianType =  Tiff ::TIFF_ENDIAN_BIG;
        } else {
            throw new  PdfException ( "Not a tiff file or Tiff corrupt. No byte order indication found" );
        }

        $version = $this->unpackBytes( Tiff ::UNPACK_TYPE_SHORT, fread($imageFile, 2));

        if($version != 42) {
            throw new  PdfException ( "Not a tiff file or Tiff corrupt. Incorrect version number." );
        }
        $ifdOffset = $this->unpackBytes( Tiff ::UNPACK_TYPE_LONG, fread($imageFile, 4));

        $fileStats = fstat($imageFile);
        $this->_fileSize = $fileStats['size'];

        /*
         * Tiff files are stored as a series \of Image File Directories (IFD) each direcctory
         * has a specific number \of entries each 12 bytes in length. At the end \of the directories
         * is four bytes pointing to the offset \of the next IFD.
         */

        while($ifdOffset > 0) {
            if(fseek($imageFile, $ifdOffset, SEEK_SET) == -1 || $ifdOffset+2 >= $this->_fileSize) {
                throw new  PdfException ("Could not seek to the image file directory as indexed by the file. Likely cause is TIFF corruption. Offset: ". $ifdOffset);
            }

            $numDirEntries = $this->unpackBytes( Tiff ::UNPACK_TYPE_SHORT, fread($imageFile, 2));

            /*
             * Since we now know how many entries are in this (IFD) we can extract the data.
             * The format \of a TIFF directory entry is:
             *
             * 2 bytes (short) tag code; See TIFF_TAG constants at the top \for supported values. (There are many more in the spec)
             * 2 bytes (short) field type
             * 4 bytes (long) number \of values, or value count.
             * 4 bytes (mixed) data if the data will fit into 4 bytes or an offset if the data is too large.
             */
            \for($dirEntryIdx = 1; $dirEntryIdx <= $numDirEntries; $dirEntryIdx++) {
                $tag         = $this->unpackBytes( Tiff ::UNPACK_TYPE_SHORT, fread($imageFile, 2));
                $fieldType   = $this->unpackBytes( Tiff ::UNPACK_TYPE_SHORT, fread($imageFile, 2));
                $valueCount  = $this->unpackBytes( Tiff ::UNPACK_TYPE_LONG, fread($imageFile, 4));

                switch($fieldType) {
                    case  Tiff ::TIFF_FIELD_TYPE_BYTE:
                        $fieldLength = $valueCount;
                        break;
                    case  Tiff ::TIFF_FIELD_TYPE_ASCII:
                        $fieldLength = $valueCount;
                        break;
                    case  Tiff ::TIFF_FIELD_TYPE_SHORT:
                        $fieldLength = $valueCount * 2;
                        break;
                    case  Tiff ::TIFF_FIELD_TYPE_LONG:
                        $fieldLength = $valueCount * 4;
                        break;
                    case  Tiff ::TIFF_FIELD_TYPE_RATIONAL:
                        $fieldLength = $valueCount * 8;
                        break;
                    default:
                        $fieldLength = $valueCount;
                }

                $offsetBytes = fread($imageFile, 4);

                if($fieldLength <= 4) {
                    switch($fieldType) {
                        case  Tiff ::TIFF_FIELD_TYPE_BYTE:
                            $value = $this->unpackBytes( Tiff ::UNPACK_TYPE_BYTE, $offsetBytes);
                            break;
                        case  Tiff ::TIFF_FIELD_TYPE_ASCII:
                            //Fall through to next case
                        case  Tiff ::TIFF_FIELD_TYPE_LONG:
                            $value = $this->unpackBytes( Tiff ::UNPACK_TYPE_LONG, $offsetBytes);
                            break;
                        case  Tiff ::TIFF_FIELD_TYPE_SHORT:
                            //Fall through to next case
                        default:
                            $value = $this->unpackBytes( Tiff ::UNPACK_TYPE_SHORT, $offsetBytes);
                    }
                } else {
                    $refOffset = $this->unpackBytes( Tiff ::UNPACK_TYPE_LONG, $offsetBytes);
                }
                /*
                 * Linear tag processing is probably not the best way to do this. I've processed the tags according to the
                 * Tiff 6 specification and make some assumptions about when tags will be < 4 bytes and fit into $value and when
                 * they will be > 4 bytes and require seek/extraction \of the offset. Same goes \for extracting arrays \of data, like
                 * the data offsets and length. This should be fixed in the future.
                 */
                switch($tag) {
                    case  Tiff ::TIFF_TAG_IMAGE_WIDTH:
                        $this->_width = $value;
                        break;
                    case  Tiff ::TIFF_TAG_IMAGE_LENGTH:
                        $this->_height = $value;
                        break;
                    case  Tiff ::TIFF_TAG_BITS_PER_SAMPLE:
                        if($valueCount>1) {
                            $fp = ftell($imageFile);
                            fseek($imageFile, $refOffset, SEEK_SET);
                            $this->_bitsPerSample = $this->unpackBytes( Tiff ::UNPACK_TYPE_SHORT, fread($imageFile, 2));
                            fseek($imageFile, $fp, SEEK_SET);
                        } else {
                            $this->_bitsPerSample = $value;
                        }
                        break;
                    case  Tiff ::TIFF_TAG_COMPRESSION:
                        $this->_compression = $value;
                        switch($value) {
                            case  Tiff ::TIFF_COMPRESSION_UNCOMPRESSED:
                                $this->_filter = 'None';
                                break;
                            case  Tiff ::TIFF_COMPRESSION_CCITT1D:
                                //Fall through to next case
                            case  Tiff ::TIFF_COMPRESSION_GROUP_3_FAX:
                                //Fall through to next case
                            case  Tiff ::TIFF_COMPRESSION_GROUP_4_FAX:
                                $this->_filter = 'CCITTFaxDecode';
                                throw new  PdfException ("CCITTFaxDecode Compression Mode Not Currently Supported");
                                break;
                            case  Tiff ::TIFF_COMPRESSION_LZW:
                                $this->_filter = 'LZWDecode';
                                throw new  PdfException ("LZWDecode Compression Mode Not Currently Supported");
                                break;
                            case  Tiff ::TIFF_COMPRESSION_JPEG:
                                $this->_filter = 'DCTDecode'; //Should work, doesnt...
                throw new  PdfException ("JPEG Compression Mode Not Currently Supported");
                                break;
                            case  Tiff ::TIFF_COMPRESSION_FLATE:
                                //fall through to next case
                            case  Tiff ::TIFF_COMPRESSION_FLATE_OBSOLETE_CODE:
                                $this->_filter = 'FlateDecode';
                throw new  PdfException ("ZIP/Flate Compression Mode Not Currently Supported");
                                break;
                            case  Tiff ::TIFF_COMPRESSION_PACKBITS:
                                $this->_filter = 'RunLengthDecode';
                                break;
                        }
                        break;
                    case  Tiff ::TIFF_TAG_PHOTOMETRIC_INTERPRETATION:
                        $this->_colorCode = $value;
                        $this->_whiteIsZero = false;
                        $this->_blackIsZero = false;
                        switch($value) {
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_WHITE_IS_ZERO:
                                $this->_whiteIsZero = true;
                                $this->_colorSpace = 'DeviceGray';
                                break;
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_BLACK_IS_ZERO:
                                $this->_blackIsZero = true;
                                $this->_colorSpace = 'DeviceGray';
                                break;
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_YCBCR:
                                //fall through to next case
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_RGB:
                                $this->_colorSpace = 'DeviceRGB';
                                break;
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_RGB_INDEXED:
                                $this->_colorSpace = 'Indexed';
                                break;
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_CMYK:
                                $this->_colorSpace = 'DeviceCMYK';
                                break;
                            case  Tiff ::TIFF_PHOTOMETRIC_INTERPRETATION_CIELAB:
                                $this->_colorSpace = 'Lab';
                                break;
                            default:
                                throw new  PdfException ('TIFF: Unknown or Unsupported Color Type: '. $value);
                        }
                        break;
                    case  Tiff ::TIFF_TAG_STRIP_OFFSETS:
                        if($valueCount>1) {
                            $format = ($this->_endianType ==  Tiff ::TIFF_ENDIAN_LITTLE)?'V*':'N*';
                            $fp = ftell($imageFile);
                            fseek($imageFile, $refOffset, SEEK_SET);
                            $stripOffsetsBytes = fread($imageFile, $fieldLength);
                            $this->_imageDataOffset = unpack($format, $stripOffsetsBytes);
                            fseek($imageFile, $fp, SEEK_SET);
                        } else {
                            $this->_imageDataOffset = $value;
                        }
                        break;
                   case  Tiff ::TIFF_TAG_STRIP_BYTE_COUNTS:
                        if($valueCount>1) {
                            $format = ($this->_endianType ==  Tiff ::TIFF_ENDIAN_LITTLE)?'V*':'N*';
                            $fp = ftell($imageFile);
                            fseek($imageFile, $refOffset, SEEK_SET);
                            $stripByteCountsBytes = fread($imageFile, $fieldLength);
                            $this->_imageDataLength = unpack($format, $stripByteCountsBytes);
                            fseek($imageFile, $fp, SEEK_SET);
                        } else {
                            $this->_imageDataLength = $value;
                        }
                        break;
                    default:
                        //For debugging. It should be harmless to ignore unknown tags, though there is some good info in them.
                        //echo "Unknown tag detected: ". $tag . " value: ". $value;
                }
            }
            $ifdOffset = $this->unpackBytes( Tiff ::UNPACK_TYPE_LONG, fread($imageFile, 4));
        }

        if(!isset($this->_imageDataOffset) || !isset($this->_imageDataLength)) {
            throw new  PdfException ("TIFF: The image processed did not contain image data as expected.");
        }

        $imageDataBytes = '';
        if(is_array($this->_imageDataOffset)) {
            if(!is_array($this->_imageDataLength)) {
                throw new  PdfException ("TIFF: The image contained multiple data offsets but not multiple data lengths. Tiff may be corrupt.");
            }
            foreach($this->_imageDataOffset as $idx => $offset) {
                fseek($imageFile, $this->_imageDataOffset[$idx], SEEK_SET);
                $imageDataBytes .= fread($imageFile, $this->_imageDataLength[$idx]);
            }
        } else {
            fseek($imageFile, $this->_imageDataOffset, SEEK_SET);
            $imageDataBytes = fread($imageFile, $this->_imageDataLength);
        }
        if($imageDataBytes === '') {
            throw new  PdfException ("TIFF: No data. Image Corruption");
        }

        fclose($imageFile);

        parent::__construct();

        $imageDictionary = $this->_resource->dictionary;
        if(!isset($this->_width) || !isset($this->_width)) {
            throw new  PdfException ("Problem reading tiff file. Tiff is probably corrupt.");
        }

        $this->_imageProperties = array();
        $this->_imageProperties['bitDepth'] = $this->_bitsPerSample;
        $this->_imageProperties['fileSize'] = $this->_fileSize;
        $this->_imageProperties['TIFFendianType'] = $this->_endianType;
        $this->_imageProperties['TIFFcompressionType'] = $this->_compression;
        $this->_imageProperties['TIFFwhiteIsZero'] = $this->_whiteIsZero;
        $this->_imageProperties['TIFFblackIsZero'] = $this->_blackIsZero;
        $this->_imageProperties['TIFFcolorCode'] = $this->_colorCode;
        $this->_imageProperties['TIFFimageDataOffset'] = $this->_imageDataOffset;
        $this->_imageProperties['TIFFimageDataLength'] = $this->_imageDataLength;
        $this->_imageProperties['PDFfilter'] = $this->_filter;
        $this->_imageProperties['PDFcolorSpace'] = $this->_colorSpace;

        $imageDictionary->Width            = new  Numeric ($this->_width);
        if($this->_whiteIsZero === true) {
            $imageDictionary->Decode       = new  PdfElementArray (array(new  Numeric (1), new  Numeric (0)));
        }
        $imageDictionary->Height           = new  Numeric ($this->_height);
        $imageDictionary->ColorSpace       = new  Name ($this->_colorSpace);
        $imageDictionary->BitsPerComponent = new  Numeric ($this->_bitsPerSample);
        if(isset($this->_filter) && $this->_filter != 'None') {
            $imageDictionary->Filter = new  Name ($this->_filter);
        }

        $this->_resource->value = $imageDataBytes;
        $this->_resource->skipFilters();
    }
    /**
     * Image width (defined in  Image _Interface)
     */
    public function getPixelWidth() {
        return $this->_width;
    }

    /**
     * Image height (defined in  Image _Interface)
     */
    public function getPixelHeight() {
        return $this->_height;
    }

    /**
     * Image properties (defined in  Image _Interface)
     */
    public function getProperties() {
        return $this->_imageProperties;
    }
}

