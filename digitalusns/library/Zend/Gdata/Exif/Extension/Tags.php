<?php

namespace Zend\Gdata\Exif\Extension;



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
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  Extension 
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * @see  Exif 
 */
require_once 'Zend/Gdata/Exif.php';

/**
 * @see  Distance 
 */
require_once 'Zend/Gdata/Exif/Extension/Distance.php';

/**
 * @see  Exposure 
 */
require_once 'Zend/Gdata/Exif/Extension/Exposure.php';

/**
 * @see  Flash 
 */
require_once 'Zend/Gdata/Exif/Extension/Flash.php';

/**
 * @see  FocalLength 
 */
require_once 'Zend/Gdata/Exif/Extension/FocalLength.php';

/**
 * @see  FStop 
 */
require_once 'Zend/Gdata/Exif/Extension/FStop.php';

/**
 * @see  ImageUniqueId 
 */
require_once 'Zend/Gdata/Exif/Extension/ImageUniqueId.php';

/**
 * @see  Iso 
 */
require_once 'Zend/Gdata/Exif/Extension/Iso.php';

/**
 * @see  Make 
 */
require_once 'Zend/Gdata/Exif/Extension/Make.php';

/**
 * @see  Model 
 */
require_once 'Zend/Gdata/Exif/Extension/Model.php';

/**
 * @see  Time 
 */
require_once 'Zend/Gdata/Exif/Extension/Time.php';

/**
 * Represents the exif:tags element used by the Gdata Exif extensions.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Exif\Extension\ImageUniqueId as ImageUniqueId;
use Zend\Gdata\Exif\Extension\FocalLength as FocalLength;
use Zend\Gdata\Exif\Extension\Distance as Distance;
use Zend\Gdata\Exif\Extension\Exposure as Exposure;
use Zend\Gdata\Exif\Extension\Flash as Flash;
use Zend\Gdata\Exif\Extension\FStop as FStop;
use Zend\Gdata\Exif\Extension\Model as Model;
use Zend\Gdata\Exif\Extension\Make as Make;
use Zend\Gdata\Exif\Extension\Time as Time;
use Zend\Gdata\Exif\Extension\Iso as Iso;
use Zend\Gdata\Extension as Extension;
use Zend\Gdata\Exif as Exif;




class  Tags  extends  Extension 
{

    protected $_rootNamespace = 'exif';
    protected $_rootElement = 'tags';
   
    /**
     * exif:distance value
     * 
     * @var  Distance 
     */ 
    protected $_distance = null;

    /**
     * exif:exposure value
     * 
     * @var  Exposure 
     */ 
    protected $_exposure = null;

    /**
     * exif:flash value
     * 
     * @var  Flash 
     */ 
    protected $_flash = null;

    /**
     * exif:focalLength value
     * 
     * @var  FocalLength 
     */ 
    protected $_focalLength = null;

    /**
     * exif:fStop value
     * 
     * @var  FStop 
     */ 
    protected $_fStop = null;

    /**
     * exif:imageUniqueID value
     * 
     * @var  ImageUniqueId 
     */ 
    protected $_imageUniqueId = null;

    /**
     * exif:iso value
     * 
     * @var  Iso 
     */ 
    protected $_iso = null;

    /**
     * exif:make value
     * 
     * @var  Make 
     */ 
    protected $_make = null;

    /**
     * exif:model value
     * 
     * @var  Model 
     */ 
    protected $_model = null;

    /**
     * exif:time value
     * 
     * @var  Time 
     */ 
    protected $_time = null;
    
    /**
     * Constructs a new  Tags  object.
     *
     * @param  Distance  $distance (optional) The exif:distance 
     *          value to be set in the constructed object.
     * @param  Exposure  $exposure (optional) The exif:exposure 
     *          value to be set in the constructed object.
     * @param  Flash  $flash (optional) The exif:flash
     *          value to be set in the constructed object.
     * @param  FocalLength $focalLength (optional) The exif:focallength
     *          value to be set in the constructed object.
     * @param  FStop  $fStop (optional) The exif:fstop 
     *          value to be set in the constructed object.
     * @param  ImageUniqueId  $imageUniqueId (optional) The exif:imageUniqueID 
     *          value to be set in the constructed object.
     * @param  Iso  $iso (optional) The exif:iso 
     *          value to be set in the constructed object.
     * @param  Make  $make (optional) The exif:make 
     *          value to be set in the constructed object.
     * @param  Model  $model (optional) The exif:model 
     *          value to be set in the constructed object.
     * @param  Time  $time (optional) The exif:time 
     *          value to be set in the constructed object.
     */
    public function __construct($distance = null, $exposure = null,
            $flash = null, $focalLength = null, $fStop = null,
            $imageUniqueId = null, $iso = null, $make = null,
            $model = null, $time = null) 
    {
        foreach ( Exif ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct();
        $this->setDistance($distance);
        $this->setExposure($exposure);
        $this->setFlash($flash);
        $this->setFocalLength($focalLength);
        $this->setFStop($fStop);
        $this->setImageUniqueId($imageUniqueId);
        $this->setIso($iso);
        $this->setMake($make);
        $this->setModel($model);
        $this->setTime($time);
    }
    
    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text \for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     *          child properties.
     */
    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_distance !== null) {
            $element->appendChild($this->_distance->getDOM($element->ownerDocument));
        }
        if ($this->_exposure !== null) {
            $element->appendChild($this->_exposure->getDOM($element->ownerDocument));
        }
        if ($this->_flash !== null) {
            $element->appendChild($this->_flash->getDOM($element->ownerDocument));
        }
        if ($this->_focalLength !== null) {
            $element->appendChild($this->_focalLength->getDOM($element->ownerDocument));
        }
        if ($this->_fStop !== null) {
            $element->appendChild($this->_fStop->getDOM($element->ownerDocument));
        }
        if ($this->_imageUniqueId !== null) {
            $element->appendChild($this->_imageUniqueId->getDOM($element->ownerDocument));
        }
        if ($this->_iso !== null) {
            $element->appendChild($this->_iso->getDOM($element->ownerDocument));
        }
        if ($this->_make !== null) {
            $element->appendChild($this->_make->getDOM($element->ownerDocument));
        }
        if ($this->_model !== null) {
            $element->appendChild($this->_model->getDOM($element->ownerDocument));
        }
        if ($this->_time !== null) {
            $element->appendChild($this->_time->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects \of the appropriate type and
     * stores them as members \of this entry based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('exif') . ':' . 'distance'; 
                $distance = new  Distance ();
                $distance->transferFromDOM($child);
                $this->_distance = $distance;
                break;
            case $this->lookupNamespace('exif') . ':' . 'exposure'; 
                $exposure = new  Exposure ();
                $exposure->transferFromDOM($child);
                $this->_exposure = $exposure;
                break;
            case $this->lookupNamespace('exif') . ':' . 'flash'; 
                $flash = new  Flash ();
                $flash->transferFromDOM($child);
                $this->_flash = $flash;
                break;
            case $this->lookupNamespace('exif') . ':' . 'focallength'; 
                $focalLength = new  FocalLength ();
                $focalLength->transferFromDOM($child);
                $this->_focalLength = $focalLength;
                break;
            case $this->lookupNamespace('exif') . ':' . 'fstop'; 
                $fStop = new  FStop ();
                $fStop->transferFromDOM($child);
                $this->_fStop = $fStop;
                break;
            case $this->lookupNamespace('exif') . ':' . 'imageUniqueID'; 
                $imageUniqueId = new  ImageUniqueId ();
                $imageUniqueId->transferFromDOM($child);
                $this->_imageUniqueId = $imageUniqueId;
                break;
            case $this->lookupNamespace('exif') . ':' . 'iso'; 
                $iso = new  Iso ();
                $iso->transferFromDOM($child);
                $this->_iso = $iso;
                break;
            case $this->lookupNamespace('exif') . ':' . 'make'; 
                $make = new  Make ();
                $make->transferFromDOM($child);
                $this->_make = $make;
                break;
            case $this->lookupNamespace('exif') . ':' . 'model'; 
                $model = new  Model ();
                $model->transferFromDOM($child);
                $this->_model = $model;
                break;
            case $this->lookupNamespace('exif') . ':' . 'time'; 
                $time = new  Time ();
                $time->transferFromDOM($child);
                $this->_time = $time;
                break;
        }
    }
    
    /**
     * Get the value \for this element's distance attribute.
     *
     * @see setDistance
     * @return  Distance  The requested attribute.
     */
    public function getDistance()
    {
        return $this->_distance;
    }
    
    /**
     * Set the value \for this element's distance attribute.
     *
     * @param  Distance  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setDistance($value)
    {
        $this->_distance = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's exposure attribute.
     *
     * @see setExposure
     * @return  Exposure  The requested attribute.
     */
    public function getExposure()
    {
        return $this->_exposure;
    }
    
    /**
     * Set the value \for this element's exposure attribute.
     *
     * @param  Exposure  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setExposure($value)
    {
        $this->_exposure = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's flash attribute.
     *
     * @see setFlash
     * @return  Flash  The requested attribute.
     */
    public function getFlash()
    {
        return $this->_flash;
    }
    
    /**
     * Set the value \for this element's flash attribute.
     *
     * @param  Flash  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setFlash($value)
    {
        $this->_flash = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's name attribute.
     *
     * @see setFocalLength
     * @return  FocalLength  The requested attribute.
     */
    public function getFocalLength()
    {
        return $this->_focalLength;
    }
    
    /**
     * Set the value \for this element's focalLength attribute.
     *
     * @param  FocalLength  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setFocalLength($value)
    {
        $this->_focalLength = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's fStop attribute.
     *
     * @see setFStop
     * @return  FStop  The requested attribute.
     */
    public function getFStop()
    {
        return $this->_fStop;
    }
    
    /**
     * Set the value \for this element's fStop attribute.
     *
     * @param  FStop  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setFStop($value)
    {
        $this->_fStop = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's imageUniqueId attribute.
     *
     * @see setImageUniqueId
     * @return  ImageUniqueId  The requested attribute.
     */
    public function getImageUniqueId()
    {
        return $this->_imageUniqueId;
    }

    /**
     * Set the value \for this element's imageUniqueId attribute.
     *
     * @param  ImageUniqueId  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setImageUniqueId($value)
    {
        $this->_imageUniqueId = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's iso attribute.
     *
     * @see setIso
     * @return  Iso  The requested attribute.
     */
    public function getIso()
    {
        return $this->_iso;
    }

    /**
     * Set the value \for this element's iso attribute.
     *
     * @param  Iso  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setIso($value)
    {
        $this->_iso = $value;
        return $this;
    }
    /**
     * Get the value \for this element's make attribute.
     *
     * @see setMake
     * @return  Make  The requested attribute.
     */
    public function getMake()
    {
        return $this->_make;
    }

    /**
     * Set the value \for this element's make attribute.
     *
     * @param  Make  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setMake($value)
    {
        $this->_make = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's model attribute.
     *
     * @see setModel
     * @return  Model  The requested attribute.
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Set the value \for this element's model attribute.
     *
     * @param  Model  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setModel($value)
    {
        $this->_model = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's time attribute.
     *
     * @see setTime
     * @return  Time  The requested attribute.
     */
    public function getTime()
    {
        return $this->_time;
    }

    /**
     * Set the value \for this element's time attribute.
     *
     * @param  Time  $value The desired value \for this attribute.
     * @return  Tags  Provides a fluent interface
     */
    public function setTime($value)
    {
        $this->_time = $value;
        return $this;
    }

}
