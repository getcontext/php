<?php

namespace Zend\Gdata\Photos;



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
 * @see  Photos 
 */
require_once 'Zend/Gdata/Photos.php';

/**
 * @see  Feed 
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * @see  Photos _PhotoEntry
 */
require_once 'Zend/Gdata/Photos/PhotoEntry.php';

/**
 * \Data model \for a collection \of photo entries, usually 
 * provided by the Picasa servers.
 * 
 * For information on requesting this feed from a server, see the
 * service class,  Photos .
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Photos\Extension\CommentingEnabled as CommentingEnabled;
use Zend\Gdata\Photos\Extension\CommentCount as CommentCount;
use Zend\Gdata\Media\Extension\MediaGroup as MediaGroup;
use Zend\Gdata\Photos\Extension\Timestamp as Timestamp;
use Zend\Gdata\Photos\Extension\Checksum as Checksum;
use Zend\Gdata\Photos\Extension\Version as Version;
use Zend\Gdata\Photos\Extension\AlbumId as AlbumId;
use Zend\Gdata\Photos\Extension\Height as Height;
use Zend\Gdata\Photos\Extension\Client as Client;
use Zend\Gdata\Photos\Extension\Width as Width;
use Zend\Gdata\Photos\Extension\Size as Size;
use Zend\Gdata\Photos\Extension\Id as Id;
use Zend\Gdata\App\Exception as GdataAppException;
use Zend\Gdata\App\Entry as Entry;
use Zend\Gdata\Photos as Photos;
use Zend\Gdata\Feed as Feed;




class  PhotoFeed  extends  Feed 
{
    
    /**
     * gphoto:id element
     *
     * @var  Id 
     */
    protected $_gphotoId = null;
    
    /**
     * gphoto:albumid element
     *
     * @var  AlbumId 
     */
    protected $_gphotoAlbumId = null;
    
    /**
     * gphoto:version element
     *
     * @var  Version 
     */
    protected $_gphotoVersion = null;
    
    /**
     * gphoto:width element
     *
     * @var  Width 
     */
    protected $_gphotoWidth = null;
    
    /**
     * gphoto:height element
     *
     * @var  Height 
     */
    protected $_gphotoHeight = null;
    
    /**
     * gphoto:size element
     *
     * @var  Size 
     */
    protected $_gphotoSize = null;
    
    /**
     * gphoto:client element
     *
     * @var  Client 
     */
    protected $_gphotoClient = null;
    
    /**
     * gphoto:checksum element
     *
     * @var  Checksum 
     */
    protected $_gphotoChecksum = null;
    
    /**
     * gphoto:timestamp element
     *
     * @var  Timestamp 
     */
    protected $_gphotoTimestamp = null;
    
    /**
     * gphoto:commentCount element
     *
     * @var  CommentCount 
     */
    protected $_gphotoCommentCount = null;
    
    /**
     * gphoto:commentingEnabled element
     *
     * @var  CommentingEnabled 
     */
    protected $_gphotoCommentingEnabled = null;
    
    /**
     * media:group element
     *
     * @var  MediaGroup 
     */
    protected $_mediaGroup = null;
    
    protected $_entryClassName = '\Zend\Gdata\Photos\PhotoEntry';
    protected $_feedClassName = '\Zend\Gdata\Photos\PhotoFeed';

    protected $_entryKindClassMapping = array(
        'http://schemas.google.com/photos/2007#comment' => '\Zend\Gdata\Photos\CommentEntry',
        'http://schemas.google.com/photos/2007#tag' => '\Zend\Gdata\Photos\TagEntry'
    );

    public function __construct($element = null)
    {
        foreach ( Photos ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_gphotoId != null) {
            $element->appendChild($this->_gphotoId->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoVersion != null) {
            $element->appendChild($this->_gphotoVersion->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoWidth != null) {
            $element->appendChild($this->_gphotoWidth->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoHeight != null) {
            $element->appendChild($this->_gphotoHeight->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoSize != null) {
            $element->appendChild($this->_gphotoSize->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoClient != null) {
            $element->appendChild($this->_gphotoClient->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoChecksum != null) {
            $element->appendChild($this->_gphotoChecksum->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoTimestamp != null) {
            $element->appendChild($this->_gphotoTimestamp->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentingEnabled != null) {
            $element->appendChild($this->_gphotoCommentingEnabled->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentCount != null) {
            $element->appendChild($this->_gphotoCommentCount->getDOM($element->ownerDocument));
        }
        if ($this->_mediaGroup != null) {
            $element->appendChild($this->_mediaGroup->getDOM($element->ownerDocument));
        }

        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) {
            case $this->lookupNamespace('gphoto') . ':' . 'id';
                $id = new  Id ();
                $id->transferFromDOM($child);
                $this->_gphotoId = $id;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'version';
                $version = new  Version ();
                $version->transferFromDOM($child);
                $this->_gphotoVersion = $version;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'albumid';
                $albumid = new  AlbumId ();
                $albumid->transferFromDOM($child);
                $this->_gphotoAlbumId = $albumid;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'width';
                $width = new  Width ();
                $width->transferFromDOM($child);
                $this->_gphotoWidth = $width;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'height';
                $height = new  Height ();
                $height->transferFromDOM($child);
                $this->_gphotoHeight = $height;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'size';
                $size = new  Size ();
                $size->transferFromDOM($child);
                $this->_gphotoSize = $size;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'client';
                $client = new  Client ();
                $client->transferFromDOM($child);
                $this->_gphotoClient = $client;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'checksum';
                $checksum = new  Checksum ();
                $checksum->transferFromDOM($child);
                $this->_gphotoChecksum = $checksum;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'timestamp';
                $timestamp = new  Timestamp ();
                $timestamp->transferFromDOM($child);
                $this->_gphotoTimestamp = $timestamp;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'commentingEnabled';
                $commentingEnabled = new  CommentingEnabled ();
                $commentingEnabled->transferFromDOM($child);
                $this->_gphotoCommentingEnabled = $commentingEnabled;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'commentCount';
                $commentCount = new  CommentCount ();
                $commentCount->transferFromDOM($child);
                $this->_gphotoCommentCount = $commentCount;
                break;
            case $this->lookupNamespace('media') . ':' . 'group'; 
                $mediaGroup = new  MediaGroup ();
                $mediaGroup->transferFromDOM($child);
                $this->_mediaGroup = $mediaGroup;
                break;
            case $this->lookupNamespace('atom') . ':' . 'entry':
                $entryClassName = $this->_entryClassName;
                $tmpEntry = new  Entry ($child);
                $categories = $tmpEntry->getCategory();
                foreach ($categories as $category) {
                    if ($category->scheme ==  Photos ::KIND_PATH &&
                        $this->_entryKindClassMapping[$category->term] != "") {
                            $entryClassName = $this->_entryKindClassMapping[$category->term];
                            break;
                    } else {
                        require_once 'Zend/Gdata/App/Exception.php';
                        throw new  GdataAppException ('Entry is missing kind declaration.');
                    }
                }

                $newEntry = new $entryClassName($child);
                $newEntry->setHttpClient($this->getHttpClient());
                $this->_entry[] = $newEntry;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * Get the value \for this element's gphoto:id attribute.
     *
     * @see setGphotoId
     * @return string The requested attribute.
     */
    public function getGphotoId()
    {
        return $this->_gphotoId;
    }

    /**
     * Set the value \for this element's gphoto:id attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Id  The element being modified.
     */
    public function setGphotoId($value)
    {
        $this->_gphotoId = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:version attribute.
     *
     * @see setGphotoVersion
     * @return string The requested attribute.
     */
    public function getGphotoVersion()
    {
        return $this->_gphotoVersion;
    }

    /**
     * Set the value \for this element's gphoto:version attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Version  The element being modified.
     */
    public function setGphotoVersion($value)
    {
        $this->_gphotoVersion = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:albumid attribute.
     *
     * @see setGphotoAlbumId
     * @return string The requested attribute.
     */
    public function getGphotoAlbumId()
    {
        return $this->_gphotoAlbumId;
    }

    /**
     * Set the value \for this element's gphoto:albumid attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  AlbumId  The element being modified.
     */
    public function setGphotoAlbumId($value)
    {
        $this->_gphotoAlbumId = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:width attribute.
     *
     * @see setGphotoWidth
     * @return string The requested attribute.
     */
    public function getGphotoWidth()
    {
        return $this->_gphotoWidth;
    }

    /**
     * Set the value \for this element's gphoto:width attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Width  The element being modified.
     */
    public function setGphotoWidth($value)
    {
        $this->_gphotoWidth = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:height attribute.
     *
     * @see setGphotoHeight
     * @return string The requested attribute.
     */
    public function getGphotoHeight()
    {
        return $this->_gphotoHeight;
    }

    /**
     * Set the value \for this element's gphoto:height attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Height  The element being modified.
     */
    public function setGphotoHeight($value)
    {
        $this->_gphotoHeight = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:size attribute.
     *
     * @see setGphotoSize
     * @return string The requested attribute.
     */
    public function getGphotoSize()
    {
        return $this->_gphotoSize;
    }

    /**
     * Set the value \for this element's gphoto:size attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Size  The element being modified.
     */
    public function setGphotoSize($value)
    {
        $this->_gphotoSize = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:client attribute.
     *
     * @see setGphotoClient
     * @return string The requested attribute.
     */
    public function getGphotoClient()
    {
        return $this->_gphotoClient;
    }

    /**
     * Set the value \for this element's gphoto:client attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Client  The element being modified.
     */
    public function setGphotoClient($value)
    {
        $this->_gphotoClient = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:checksum attribute.
     *
     * @see setGphotoChecksum
     * @return string The requested attribute.
     */
    public function getGphotoChecksum()
    {
        return $this->_gphotoChecksum;
    }

    /**
     * Set the value \for this element's gphoto:checksum attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Checksum  The element being modified.
     */
    public function setGphotoChecksum($value)
    {
        $this->_gphotoChecksum = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:timestamp attribute.
     *
     * @see setGphotoTimestamp
     * @return string The requested attribute.
     */
    public function getGphotoTimestamp()
    {
        return $this->_gphotoTimestamp;
    }

    /**
     * Set the value \for this element's gphoto:timestamp attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Timestamp  The element being modified.
     */
    public function setGphotoTimestamp($value)
    {
        $this->_gphotoTimestamp = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:commentCount attribute.
     *
     * @see setGphotoCommentCount
     * @return string The requested attribute.
     */
    public function getGphotoCommentCount()
    {
        return $this->_gphotoCommentCount;
    }

    /**
     * Set the value \for this element's gphoto:commentCount attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  CommentCount  The element being modified.
     */
    public function setGphotoCommentCount($value)
    {
        $this->_gphotoCommentCount = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's gphoto:commentingEnabled attribute.
     *
     * @see setGphotoCommentingEnabled
     * @return string The requested attribute.
     */
    public function getGphotoCommentingEnabled()
    {
        return $this->_gphotoCommentingEnabled;
    }

    /**
     * Set the value \for this element's gphoto:commentingEnabled attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  CommentingEnabled  The element being modified.
     */
    public function setGphotoCommentingEnabled($value)
    {
        $this->_gphotoCommentingEnabled = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's media:group attribute.
     *
     * @see setMediaGroup
     * @return string The requested attribute.
     */
    public function getMediaGroup()
    {
        return $this->_mediaGroup;
    }

    /**
     * Set the value \for this element's media:group attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  MediaGroup  The element being modified.
     */
    public function setMediaGroup($value)
    {
        $this->_mediaGroup = $value;
        return $this;
    }

}
