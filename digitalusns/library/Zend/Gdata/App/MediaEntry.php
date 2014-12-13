<?php

namespace Zend\Gdata\App;



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
 * @see  Entry 
 */
require_once 'Zend/Gdata/App/Entry.php';

/**
 * @see  MediaSource 
 */
require_once 'Zend/Gdata/App/MediaSource.php';

/**
 * @see  Mime 
 */
require_once 'Zend/Mime.php';

/**
 * @see  Message 
 */
require_once 'Zend/Mime/Message.php';


/**
 * Concrete class \for working with Atom entries containing multi-part data.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\InvalidArgumentException as GdataAppInvalidArgumentException;
use Zend\Gdata\App\MediaSource as MediaSource;
use Zend\Gdata\App\Entry as Entry;
use Zend\Mime\Message as Message;
use Zend\Mime\Part as Part;
use Zend\Mime as Mime;




class  MediaEntry  extends  Entry 
{
    /**
     * The attached MediaSource/file
     *
     * @var  MediaSource  
     */
    protected $_mediaSource = null;

    /**
     * The  Mime  object used to generate the boundary
     *
     * @var  Mime  
     */
    protected $_mime = null;
   
    /**
     * Constructs a new MediaEntry, representing XML data and optional
     * file to upload
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null, $mediaSource = null)
    {
        parent::__construct($element);
        $this->_mime = new  Mime ();
        $this->_mediaSource = $mediaSource;
    }
 
    /**
     * Return the  Mime  object associated with this MediaEntry.  This
     * object is used to generate the media boundaries.
     * 
     * @return  Mime  The  Mime  object associated with this MediaEntry.
     */
    public function getMime()
    {
        return $this->_mime;
    }
    
    /**
     * Return the MIME multipart representation \of this MediaEntry.
     *
     * @return string The MIME multipart representation \of this MediaEntry
     */
    public function encode()
    {
        $xmlData = $this->saveXML();
        if ($this->getMediaSource() === null) {
            // No attachment, just send XML \for entry
            return $xmlData;
        } else {
            $mimeMessage = new  Message ();
            $mimeMessage->setMime($this->_mime);
           
            $xmlPart = new  Part ($xmlData);
            $xmlPart->type = 'application/atom+xml';
            $xmlPart->encoding = null;
            $mimeMessage->addPart($xmlPart);
            
            $binaryPart = new  Part ($this->getMediaSource()->encode());
            $binaryPart->type = $this->getMediaSource()->getContentType();
            $binaryPart->encoding = null;
            $mimeMessage->addPart($binaryPart);

            return $mimeMessage->generateMessage();
        }
    }
   
    /**
     * Return the MediaSource object representing the file attached to this
     * MediaEntry.
     *
     * @return  MediaSource  The attached MediaSource/file
     */
    public function getMediaSource()
    {
        return $this->_mediaSource;
    }

    /**
     * Set the MediaSource object (file) \for this MediaEntry
     *
     * @param  MediaSource  $value The attached MediaSource/file
     * @return  MediaEntry  Provides a fluent interface
     */
    public function setMediaSource($value)
    {
        if ($value instanceof  MediaSource ) {
            $this->_mediaSource = $value;
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'You must specify the media data as a class that conforms to  MediaSource .');
        }
        return $this;
    }
    
    /**
     * Return the boundary used in the MIME multipart message
     *
     * @return string The boundary used in the MIME multipart message 
     */
    public function getBoundary()
    {
        return $this->_mime->boundary();
    }

}
