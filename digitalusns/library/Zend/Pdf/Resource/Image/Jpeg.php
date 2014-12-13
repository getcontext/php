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
 * JPEG image
 *
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\Resource\Image as Image;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Exception as PdfException;




class  Jpeg  extends  Image 
{

    protected $_width;
    protected $_height;
    protected $_imageProperties;

    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws  PdfException 
     */
    public function __construct($imageFileName)
    {
        if (!function_exists('gd_info')) {
            throw new  PdfException ('Image extension is not installed.');
        }

        $gd_options = gd_info();
        if (!$gd_options['JPG Support'] ) {
            throw new  PdfException ('JPG support is not configured properly.');
        }

        if (($imageInfo = getimagesize($imageFileName)) === false) {
            throw new  PdfException ('Corrupted image or image doesn\'t exist.');
        }
        if ($imageInfo[2] != IMAGETYPE_JPEG && $imageInfo[2] != IMAGETYPE_JPEG2000) {
            throw new  PdfException ('ImageType is not JPG');
        }

        parent::__construct();

        switch ($imageInfo['channels']) {
            case 3:
                $colorSpace = 'DeviceRGB';
                break;
            case 4:
                $colorSpace = 'DeviceCMYK';
                break;
            default:
                $colorSpace = 'DeviceGray';
                break;
        }

        $imageDictionary = $this->_resource->dictionary;
        $imageDictionary->Width            = new  Numeric ($imageInfo[0]);
        $imageDictionary->Height           = new  Numeric ($imageInfo[1]);
        $imageDictionary->ColorSpace       = new  Name ($colorSpace);
        $imageDictionary->BitsPerComponent = new  Numeric ($imageInfo['bits']);
        if ($imageInfo[2] == IMAGETYPE_JPEG) {
            $imageDictionary->Filter       = new  Name ('DCTDecode');
        } else if ($imageInfo[2] == IMAGETYPE_JPEG2000){
            $imageDictionary->Filter       = new  Name ('JPXDecode');
        }

        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new  PdfException ( "Can not open '$imageFileName' file \for reading." );
        }
        $byteCount = filesize($imageFileName);
        $this->_resource->value = '';
        while ( $byteCount > 0 && ($nextBlock = fread($imageFile, $byteCount)) != false ) {
            $this->_resource->value .= $nextBlock;
            $byteCount -= strlen($nextBlock);
        }
        fclose($imageFile);
        $this->_resource->skipFilters();

    $this->_width = $imageInfo[0];
    $this->_height = $imageInfo[1];
    $this->_imageProperties = array();
    $this->_imageProperties['bitDepth'] = $imageInfo['bits'];
    $this->_imageProperties['jpegImageType'] = $imageInfo[2];
    $this->_imageProperties['jpegColorType'] = $imageInfo['channels'];
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

