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
 * @see  Photos _AlbumEntry
 */
require_once 'Zend/Gdata/Photos/AlbumEntry.php';

/**
 * \Data model \for a collection \of album entries, usually 
 * provided by the servers.
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
use Zend\Gdata\Photos\Extension\Timestamp as Timestamp;
use Zend\Gdata\Photos\Extension\NumPhotos as NumPhotos;
use Zend\Gdata\Photos\Extension\Nickname as Nickname;
use Zend\Gdata\Photos\Extension\Location as Location;
use Zend\Gdata\Photos\Extension\Access as Access;
use Zend\Gdata\Photos\Extension\User as GdataPhotosExtensionUser;
use Zend\Gdata\Photos\Extension\Name as Name;
use Zend\Gdata\Photos\Extension\Id as Id;
use Zend\Gdata\App\Exception as GdataAppException;
use Zend\Gdata\App\Entry as Entry;
use Zend\Gdata\Photos as Photos;
use Zend\Gdata\Feed as Feed;




class  AlbumFeed  extends  Feed 
{
    protected $_entryClassName = '\Zend\Gdata\Photos\AlbumEntry';
    protected $_feedClassName = '\Zend\Gdata\Photos\AlbumFeed';
    
    /**
     * gphoto:id element
     *
     * @var  Id 
     */
    protected $_gphotoId = null;
    
    /**
     * gphoto:user element
     *
     * @var  GdataPhotosExtensionUser 
     */
    protected $_gphotoUser = null;
    
    /**
     * gphoto:access element
     *
     * @var  Access 
     */
    protected $_gphotoAccess = null;
    
    /**
     * gphoto:location element
     *
     * @var  Location 
     */
    protected $_gphotoLocation = null;
    
    /**
     * gphoto:nickname element
     *
     * @var  Nickname 
     */
    protected $_gphotoNickname = null;
    
    /**
     * gphoto:timestamp element
     *
     * @var  Timestamp 
     */
    protected $_gphotoTimestamp = null;
    
    /**
     * gphoto:name element
     *
     * @var  Name 
     */
    protected $_gphotoName = null;
    
    /**
     * gphoto:numphotos element
     *
     * @var  NumPhotos 
     */
    protected $_gphotoNumPhotos = null;
    
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
    
    protected $_entryKindClassMapping = array(
        'http://schemas.google.com/photos/2007#photo' => '\Zend\Gdata\Photos\PhotoEntry',
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
        if ($this->_gphotoUser != null) {
            $element->appendChild($this->_gphotoUser->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoNickname != null) {
            $element->appendChild($this->_gphotoNickname->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoName != null) {
            $element->appendChild($this->_gphotoName->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoLocation != null) {
            $element->appendChild($this->_gphotoLocation->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoAccess != null) {
            $element->appendChild($this->_gphotoAccess->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoTimestamp != null) {
            $element->appendChild($this->_gphotoTimestamp->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoNumPhotos != null) {
            $element->appendChild($this->_gphotoNumPhotos->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentingEnabled != null) {
            $element->appendChild($this->_gphotoCommentingEnabled->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoCommentCount != null) {
            $element->appendChild($this->_gphotoCommentCount->getDOM($element->ownerDocument));
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
            case $this->lookupNamespace('gphoto') . ':' . 'user';
                $user = new  GdataPhotosExtensionUser ();
                $user->transferFromDOM($child);
                $this->_gphotoUser = $user;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'nickname';
                $nickname = new  Nickname ();
                $nickname->transferFromDOM($child);
                $this->_gphotoNickname = $nickname;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'name';
                $name = new  Name ();
                $name->transferFromDOM($child);
                $this->_gphotoName = $name;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'location';
                $location = new  Location ();
                $location->transferFromDOM($child);
                $this->_gphotoLocation = $location;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'access';
                $access = new  Access ();
                $access->transferFromDOM($child);
                $this->_gphotoAccess = $access;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'timestamp';
                $timestamp = new  Timestamp ();
                $timestamp->transferFromDOM($child);
                $this->_gphotoTimestamp = $timestamp;
                break;
            case $this->lookupNamespace('gphoto') . ':' . 'numphotos';
                $numphotos = new  NumPhotos ();
                $numphotos->transferFromDOM($child);
                $this->_gphotoNumPhotos = $numphotos;
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
     * Get the value \for this element's gphoto:user attribute.
     *
     * @see setGphotoUser
     * @return string The requested attribute.
     */
    public function getGphotoUser()
    {
        return $this->_gphotoUser;
    }
    
    /**
     * Set the value \for this element's gphoto:user attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  GdataPhotosExtensionUser  The element being modified.
     */
    public function setGphotoUser($value)
    {
        $this->_gphotoUser = $value;
        return $this;
    }

    /**
     * Get the value \for this element's gphoto:access attribute.
     *
     * @see setGphotoAccess
     * @return string The requested attribute.
     */
    public function getGphotoAccess()
    {
        return $this->_gphotoAccess;
    }

    /**
     * Set the value \for this element's gphoto:access attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Access  The element being modified.
     */
    public function setGphotoAccess($value)
    {
        $this->_gphotoAccess = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's gphoto:location attribute.
     *
     * @see setGphotoLocation
     * @return string The requested attribute.
     */
    public function getGphotoLocation()
    {
        return $this->_gphotoLocation;
    }

    /**
     * Set the value \for this element's gphoto:location attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Location  The element being modified.
     */
    public function setGphotoLocation($value)
    {
        $this->_gphotoLocation = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's gphoto:name attribute.
     *
     * @see setGphotoName
     * @return string The requested attribute.
     */
    public function getGphotoName()
    {
        return $this->_gphotoName;
    }

    /**
     * Set the value \for this element's gphoto:name attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Name  The element being modified.
     */
    public function setGphotoName($value)
    {
        $this->_gphotoName = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's gphoto:numphotos attribute.
     *
     * @see setGphotoNumPhotos
     * @return string The requested attribute.
     */
    public function getGphotoNumPhotos()
    {
        return $this->_gphotoNumPhotos;
    }

    /**
     * Set the value \for this element's gphoto:numphotos attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  NumPhotos  The element being modified.
     */
    public function setGphotoNumPhotos($value)
    {
        $this->_gphotoNumPhotos = $value;
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
     * Get the value \for this element's georss:where attribute.
     *
     * @see setGeoRssWhere
     * @return string The requested attribute.
     */
    public function getGeoRssWhere()
    {
        return $this->_geoRssWhere;
    }

    /**
     * Set the value \for this element's georss:where attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return \Zend\Gdata\Geo\Extension\GeoRssWhere The element being modified.
     */
    public function setGeoRssWhere($value)
    {
        $this->_geoRssWhere = $value;
        return $this;
    }
    
    /**
     * Get the value \for this element's gphoto:nickname attribute.
     *
     * @see setGphotoNickname
     * @return string The requested attribute.
     */
    public function getGphotoNickname()
    {
        return $this->_gphotoNickname;
    }

    /**
     * Set the value \for this element's gphoto:nickname attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Nickname  The element being modified.
     */
    public function setGphotoNickname($value)
    {
        $this->_gphotoNickname = $value;
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
    
}
