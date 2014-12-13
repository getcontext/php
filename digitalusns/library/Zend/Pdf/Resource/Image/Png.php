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

/**  ElementFactory  */
require_once 'Zend/Pdf/ElementFactory.php';


/**
 * PNG image
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\String\Binary as Binary;
use Zend\Pdf\Element\Dictionary as Dictionary;
use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\Resource\Image as Image;
use Zend\Pdf\ElementFactory as ElementFactory;
use Zend\Pdf\Element\ElementArray as PdfElementArray;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Exception as PdfException;




class  Png  extends  Image 
{
    const PNG_COMPRESSION_DEFAULT_STRATEGY = 0;
    const PNG_COMPRESSION_FILTERED = 1;
    const PNG_COMPRESSION_HUFFMAN_ONLY = 2;
    const PNG_COMPRESSION_RLE = 3;

    const PNG_FILTER_NONE = 0;
    const PNG_FILTER_SUB = 1;
    const PNG_FILTER_UP = 2;
    const PNG_FILTER_AVERAGE = 3;
    const PNG_FILTER_PAETH = 4;

    const PNG_INTERLACING_DISABLED = 0;
    const PNG_INTERLACING_ENABLED = 1;

    const PNG_CHANNEL_GRAY = 0;
    const PNG_CHANNEL_RGB = 2;
    const PNG_CHANNEL_INDEXED = 3;
    const PNG_CHANNEL_GRAY_ALPHA = 4;
    const PNG_CHANNEL_RGB_ALPHA = 6;

    protected $_width;
    protected $_height;
    protected $_imageProperties;

    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws  PdfException 
     * @todo Add compression conversions to support compression strategys other than PNG_COMPRESSION_DEFAULT_STRATEGY.
     * @todo Add pre-compression filtering.
     * @todo Add interlaced image handling.
     * @todo Add support \for 16-bit images. Requires PDF version bump to 1.5 at least.
     * @todo Add processing \for all PNG chunks defined in the spec. gAMA etc.
     * @todo Fix tRNS chunk support \for Indexed Images to a SMask.
     */
    public function __construct($imageFileName)
    {
        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new  PdfException ( "Can not open '$imageFileName' file \for reading." );
        }

        parent::__construct();

        //Check if the file is a PNG
        fseek($imageFile, 1, SEEK_CUR); //First signature byte (%)
        if ('PNG' != fread($imageFile, 3)) {
            throw new  PdfException ('Image is not a PNG');
        }
        fseek($imageFile, 12, SEEK_CUR); //Signature bytes (Includes the IHDR chunk) IHDR processed linerarly because it doesnt contain a variable chunk length
        $wtmp = unpack('Ni',fread($imageFile, 4)); //Unpack a 4-Byte Long
        $width = $wtmp['i'];
        $htmp = unpack('Ni',fread($imageFile, 4));
        $height = $htmp['i'];
        $bits = ord(fread($imageFile, 1)); //Higher than 8 bit depths are only supported in later versions \of PDF.
        $color = ord(fread($imageFile, 1));

        $compression = ord(fread($imageFile, 1));
        $prefilter = ord(fread($imageFile,1));

        if (($interlacing = ord(fread($imageFile,1))) !=  Png ::PNG_INTERLACING_DISABLED) {
            throw new  PdfException ( "Only non-interlaced images are currently supported." );
        }

        $this->_width = $width;
        $this->_height = $height;
        $this->_imageProperties = array();
        $this->_imageProperties['bitDepth'] = $bits;
        $this->_imageProperties['pngColorType'] = $color;
        $this->_imageProperties['pngFilterType'] = $prefilter;
        $this->_imageProperties['pngCompressionType'] = $compression;
        $this->_imageProperties['pngInterlacingType'] = $interlacing;

        fseek($imageFile, 4, SEEK_CUR); //4 Byte Ending Sequence
        $imageData = '';

        /*
         * The following loop processes PNG chunks. 4 Byte Longs are packed first give the chunk length
         * followed by the chunk signature, a four byte code. IDAT and IEND are manditory in any PNG.
         */
        while(($chunkLengthBytes = fread($imageFile, 4)) !== false) {
            $chunkLengthtmp         = unpack('Ni', $chunkLengthBytes);
            $chunkLength            = $chunkLengthtmp['i'];
            $chunkType                      = fread($imageFile, 4);
            switch($chunkType) {
                case 'IDAT': //Image \Data
                    /*
                     * Reads the actual image data from the PNG file. Since we know at this point that the compression
                     * strategy is the default strategy, we also know that this data is Zip compressed. We will either copy
                     * the data directly to the PDF and provide the correct FlateDecode predictor, or decompress the data
                     * decode the filters and output the data as a raw pixel map.
                     */
                    $imageData .= fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'PLTE': //Palette
                    $paletteData = fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'tRNS': //Basic (non-alpha channel) transparency.
                    $trnsData = fread($imageFile, $chunkLength);
                    switch ($color) {
                        case  Png ::PNG_CHANNEL_GRAY:
                            $baseColor = ord(substr($trnsData, 1, 1));
                            $transparencyData = array(new  Numeric ($baseColor), new  Numeric ($baseColor));
                            break;

                        case  Png ::PNG_CHANNEL_RGB:
                            $red = ord(substr($trnsData,1,1));
                            $green = ord(substr($trnsData,3,1));
                            $blue = ord(substr($trnsData,5,1));
                            $transparencyData = array(new  Numeric ($red), new  Numeric ($red), new  Numeric ($green), new  Numeric ($green), new  Numeric ($blue), new  Numeric ($blue));
                            break;

                        case  Png ::PNG_CHANNEL_INDEXED:
                            //Find the first transparent color in the index, we will mask that. (This is a bit \of a hack. This should be a SMask and mask all entries values).
                            if(($trnsIdx = strpos($trnsData, chr(0))) !== false) {
                                $transparencyData = array(new  Numeric ($trnsIdx), new  Numeric ($trnsIdx));
                            }
                            break;

                        case  Png ::PNG_CHANNEL_GRAY_ALPHA:
                            // Fall through to the next case

                        case  Png ::PNG_CHANNEL_RGB_ALPHA:
                            throw new  PdfException ( "tRNS chunk illegal \for Alpha Channel Images" );
                            break;
                    }
                    fseek($imageFile, 4, SEEK_CUR); //4 Byte Ending Sequence
                    break;

                case 'IEND';
                    break 2; //End the loop too

                default:
                    fseek($imageFile, $chunkLength + 4, SEEK_CUR); //Skip the section
                    break;
            }
        }
        fclose($imageFile);

        $compressed = true;
        $imageDataTmp = '';
        $smaskData = '';
        switch ($color) {
            case  Png ::PNG_CHANNEL_RGB:
                $colorSpace = new  Name ('DeviceRGB');
                break;

            case  Png ::PNG_CHANNEL_GRAY:
                $colorSpace = new  Name ('DeviceGray');
                break;

            case  Png ::PNG_CHANNEL_INDEXED:
                if(empty($paletteData)) {
                    throw new  PdfException ( "PNG Corruption: No palette data read \for indexed type PNG." );
                }
                $colorSpace = new  PdfElementArray ();
                $colorSpace->items[] = new  Name ('Indexed');
                $colorSpace->items[] = new  Name ('DeviceRGB');
                $colorSpace->items[] = new  Numeric ((strlen($paletteData)/3-1));
                $paletteObject = $this->_objectFactory->newObject(new  Binary ($paletteData));
                $colorSpace->items[] = $paletteObject;
                break;

            case  Png ::PNG_CHANNEL_GRAY_ALPHA:
                /*
                 * To decode PNG's with alpha data we must create two images from one. One image will contain the Gray data
                 * the other will contain the Gray transparency overlay data. The former will become the object data and the latter
                 * will become the Shadow Mask (SMask).
                 */
                if($bits > 8) {
                    throw new  PdfException ("Alpha PNGs with bit depth > 8 are not yet supported");
                }

                $colorSpace = new  Name ('DeviceGray');

                $decodingObjFactory =  ElementFactory ::createFactory(1);
                $decodingStream = $decodingObjFactory->newStreamObject($imageData);
                $decodingStream->dictionary->Filter      = new  Name ('FlateDecode');
                $decodingStream->dictionary->DecodeParms = new  Dictionary ();
                $decodingStream->dictionary->DecodeParms->Predictor        = new  Numeric (15);
                $decodingStream->dictionary->DecodeParms->Columns          = new  Numeric ($width);
                $decodingStream->dictionary->DecodeParms->Colors           = new  Numeric (2);   //GreyAlpha
                $decodingStream->dictionary->DecodeParms->BitsPerComponent = new  Numeric ($bits);
                $decodingStream->skipFilters();

                $pngDataRawDecoded = $decodingStream->value;

                //Iterate every pixel and copy out gray data and alpha channel (this will be slow)
                \for($pixel = 0, $pixelcount = ($width * $height); $pixel < $pixelcount; $pixel++) {
                    $imageDataTmp .= $pngDataRawDecoded[($pixel*2)];
                    $smaskData .= $pngDataRawDecoded[($pixel*2)+1];
                }
                $compressed = false;
                $imageData  = $imageDataTmp; //Overwrite image data with the gray channel without alpha
                break;

            case  Png ::PNG_CHANNEL_RGB_ALPHA:
                /*
                 * To decode PNG's with alpha data we must create two images from one. One image will contain the RGB data
                 * the other will contain the Gray transparency overlay data. The former will become the object data and the latter
                 * will become the Shadow Mask (SMask).
                 */
                if($bits > 8) {
                    throw new  PdfException ("Alpha PNGs with bit depth > 8 are not yet supported");
                }

                $colorSpace = new  Name ('DeviceRGB');

                $decodingObjFactory =  ElementFactory ::createFactory(1);
                $decodingStream = $decodingObjFactory->newStreamObject($imageData);
                $decodingStream->dictionary->Filter      = new  Name ('FlateDecode');
                $decodingStream->dictionary->DecodeParms = new  Dictionary ();
                $decodingStream->dictionary->DecodeParms->Predictor        = new  Numeric (15);
                $decodingStream->dictionary->DecodeParms->Columns          = new  Numeric ($width);
                $decodingStream->dictionary->DecodeParms->Colors           = new  Numeric (4);   //RGBA
                $decodingStream->dictionary->DecodeParms->BitsPerComponent = new  Numeric ($bits);
                $decodingStream->skipFilters();

                $pngDataRawDecoded = $decodingStream->value;

                //Iterate every pixel and copy out rgb data and alpha channel (this will be slow)
                \for($pixel = 0, $pixelcount = ($width * $height); $pixel < $pixelcount; $pixel++) {
                    $imageDataTmp .= $pngDataRawDecoded[($pixel*4)+0] . $pngDataRawDecoded[($pixel*4)+1] . $pngDataRawDecoded[($pixel*4)+2];
                    $smaskData .= $pngDataRawDecoded[($pixel*4)+3];
                }

                $compressed = false;
                $imageData  = $imageDataTmp; //Overwrite image data with the RGB channel without alpha
                break;

            default:
                throw new  PdfException ( "PNG Corruption: Invalid color space." );
        }

        if(empty($imageData)) {
            throw new  PdfException ( "Corrupt PNG Image. Mandatory IDAT chunk not found." );
        }

        $imageDictionary = $this->_resource->dictionary;
        if(!empty($smaskData)) {
            /*
             * Includes the Alpha transparency data as a Gray Image, then assigns the image as the Shadow Mask \for the main image data.
             */
            $smaskStream = $this->_objectFactory->newStreamObject($smaskData);
            $smaskStream->dictionary->Type = new  Name ('XObject');
            $smaskStream->dictionary->Subtype = new  Name ('Image');
            $smaskStream->dictionary->Width = new  Numeric ($width);
            $smaskStream->dictionary->Height = new  Numeric ($height);
            $smaskStream->dictionary->ColorSpace = new  Name ('DeviceGray');
            $smaskStream->dictionary->BitsPerComponent = new  Numeric ($bits);
            $imageDictionary->SMask = $smaskStream;

            // Encode stream with FlateDecode filter
            $smaskStreamDecodeParms = array();
            $smaskStreamDecodeParms['Predictor']        = new  Numeric (15);
            $smaskStreamDecodeParms['Columns']          = new  Numeric ($width);
            $smaskStreamDecodeParms['Colors']           = new  Numeric (1);
            $smaskStreamDecodeParms['BitsPerComponent'] = new  Numeric (8);
            $smaskStream->dictionary->DecodeParms  = new  Dictionary ($smaskStreamDecodeParms);
            $smaskStream->dictionary->Filter       = new  Name ('FlateDecode');
        }

        if(!empty($transparencyData)) {
            //This is experimental and not properly tested.
            $imageDictionary->Mask = new  PdfElementArray ($transparencyData);
        }

        $imageDictionary->Width            = new  Numeric ($width);
        $imageDictionary->Height           = new  Numeric ($height);
        $imageDictionary->ColorSpace       = $colorSpace;
        $imageDictionary->BitsPerComponent = new  Numeric ($bits);
        $imageDictionary->Filter       = new  Name ('FlateDecode');

        $decodeParms = array();
        $decodeParms['Predictor']        = new  Numeric (15); // Optimal prediction
        $decodeParms['Columns']          = new  Numeric ($width);
        $decodeParms['Colors']           = new  Numeric ((($color== Png ::PNG_CHANNEL_RGB || $color== Png ::PNG_CHANNEL_RGB_ALPHA)?(3):(1)));
        $decodeParms['BitsPerComponent'] = new  Numeric ($bits);
        $imageDictionary->DecodeParms  = new  Dictionary ($decodeParms);

        //Include only the image IDAT section data.
        $this->_resource->value = $imageData;

        //Skip double compression
        if ($compressed) {
            $this->_resource->skipFilters();
        }
    }

    /**
     * Image width
     */
    public function getPixelWidth() {
    return $this->_width;
    }

    /**
     * Image height
     */
    public function getPixelHeight() {
        return $this->_height;
    }

    /**
     * Image properties
     */
    public function getProperties() {
        return $this->_imageProperties;
    }
}
