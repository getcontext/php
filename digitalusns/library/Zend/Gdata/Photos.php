<?php

namespace Zend\Gdata;



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
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  Gdata 
 */
require_once 'Zend/Gdata.php';

/**
 * @see  Photos _UserFeed
 */
require_once 'Zend/Gdata/Photos/UserFeed.php';

/**
 * @see  Photos _AlbumFeed
 */
require_once 'Zend/Gdata/Photos/AlbumFeed.php';

/**
 * @see  Photos _PhotoFeed
 */
require_once 'Zend/Gdata/Photos/PhotoFeed.php';

/**
 * Service class \for interacting with the Google Photos \Data API.
 * 
 * Like other service classes in this module, this class provides access via 
 * an HTTP client to Google servers \for working with entries and feeds.
 * 
 * @link http://code.google.com/apis/picasaweb/gdata.html
 *
 * @category   Zend
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\InvalidArgumentException as GdataAppInvalidArgumentException;
use Zend\Gdata\Photos\CommentEntry as CommentEntry;
use Zend\Gdata\Photos\AlbumEntry as AlbumEntry;
use Zend\Gdata\Photos\PhotoEntry as PhotoEntry;
use Zend\Gdata\App\HttpException as HttpException;
use Zend\Gdata\Photos\UserQuery as UserQuery;
use Zend\Gdata\Photos\TagEntry as TagEntry;
use Zend\Http\Client as Client;
use Zend\Gdata\Query as Query;
use Zend\Gdata as Gdata;




class  Photos  extends  Gdata 
{
    
    const PICASA_BASE_URI = 'http://picasaweb.google.com/data';
    const PICASA_BASE_FEED_URI = 'http://picasaweb.google.com/data/feed';
    const AUTH_SERVICE_NAME = 'lh2';
    
    /**
     * Default projection when interacting with the Picasa server.
     */
    const DEFAULT_PROJECTION = 'api';
    
    /**
     * The default visibility to filter events by.
     */
    const DEFAULT_VISIBILITY = 'all';
    
    /**
     * The default user to retrieve feeds \for.
     */
    const DEFAULT_USER = 'default';
    
    /**
     * Path to the user feed on the Picasa server.
     */
    const USER_PATH = 'user';
    
    /**
     * Path to album feeds on the Picasa server.
     */
    const ALBUM_PATH = 'albumid';
    
    /**
     * Path to photo feeds on the Picasa server.
     */
    const PHOTO_PATH = 'photoid';
    
    /**
     * The path to the community search feed on the Picasa server.
     */
    const COMMUNITY_SEARCH_PATH = 'all';
    
    /**
     * The path to use \for finding links to feeds within entries
     */
    const FEED_LINK_PATH = 'http://schemas.google.com/g/2005#feed';
    
    /**
     * The path to use \for the determining type \of an entry
     */
    const KIND_PATH = 'http://schemas.google.com/g/2005#kind';
    
    public static $namespaces = array(
            'gphoto' => 'http://schemas.google.com/photos/2007',
            'photo' => 'http://www.pheed.com/pheed/',
            'exif' => 'http://schemas.google.com/photos/exif/2007',
            'georss' => 'http://www.georss.org/georss',
            'gml' => 'http://www.opengis.net/gml',
            'media' => 'http://search.yahoo.com/mrss/');

    /**
     * Create  Photos  object
     * 
     * @param  Client  $client (optional) The HTTP client to use when 
     *          when communicating with the servers.
     * @param string $applicationId The identity \of the app in the form \of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('\Zend\Gdata\Photos');
        $this->registerPackage('\Zend\Gdata\Photos_Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retrieve a UserFeed containing AlbumEntries, PhotoEntries and 
     * TagEntries associated with a given user.
     *
     * @param string $userName The userName \of interest
     * @param mixed $location (optional) The location \for the feed, as a URL 
     *          or Query. If not provided, a default URL will be used instead.
     * @return  Photos _UserFeed
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getUserFeed($userName = null, $location = null)
    {
        if ($userName !== null) {
            $uri = self::PICASA_BASE_FEED_URI . '/' .
                self::DEFAULT_PROJECTION . '/' . self::USER_PATH . '/' .
                $userName;
        } else if ($location === null) {
            $uri = self::PICASA_BASE_FEED_URI . '/' .
                self::DEFAULT_PROJECTION . '/' . self::USER_PATH . '/' .
                self::DEFAULT_USER;
        } else if ($location instanceof  UserQuery ) {
            $location->setType('feed');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        
        return parent::getFeed($uri, '\Zend\Gdata\Photos\UserFeed');
    }

    /**
     * Retreive AlbumFeed object containing multiple PhotoEntry or TagEntry
     * objects.
     *
     * @param mixed $location (optional) The location \for the feed, as a URL or Query.
     * @return  Photos _AlbumFeed
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getAlbumFeed($location = null)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  UserQuery ) {
            $location->setType('feed');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\Photos\AlbumFeed');
    }

    /**
     * Retreive PhotoFeed object containing comments and tags associated 
     * with a given photo.
     *
     * @param mixed $location (optional) The location \for the feed, as a URL
     *          or Query. If not specified, the community search feed will
     *          be returned instead.
     * @return  Photos _PhotoFeed
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getPhotoFeed($location = null)
    {
        if ($location === null) {
            $uri = self::PICASA_BASE_FEED_URI . '/' .
                self::DEFAULT_PROJECTION . '/' .
                self::COMMUNITY_SEARCH_PATH;
        } else if ($location instanceof  UserQuery ) {
            $location->setType('feed');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\Photos\PhotoFeed');
    }

    /**
     * Retreive a single UserEntry object.
     *
     * @param mixed $location The location \for the feed, as a URL or Query.
     * @return  Photos _UserEntry
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getUserEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  UserQuery ) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Photos\UserEntry');
    }

    /**
     * Retreive a single AlbumEntry object.
     *
     * @param mixed $location The location \for the feed, as a URL or Query.
     * @return  AlbumEntry 
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getAlbumEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  UserQuery ) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Photos\AlbumEntry');
    }

    /**
     * Retreive a single PhotoEntry object.
     *
     * @param mixed $location The location \for the feed, as a URL or Query.
     * @return  PhotoEntry 
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getPhotoEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  UserQuery ) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Photos\PhotoEntry');
    }

    /**
     * Retreive a single TagEntry object.
     *
     * @param mixed $location The location \for the feed, as a URL or Query.
     * @return  TagEntry 
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getTagEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  UserQuery ) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Photos\TagEntry');
    }

    /**
     * Retreive a single CommentEntry object.
     *
     * @param mixed $location The location \for the feed, as a URL or Query.
     * @return  CommentEntry 
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function getCommentEntry($location)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  UserQuery ) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Photos\CommentEntry');
    }

    /**
     * Create a new album from a AlbumEntry.
     * 
     * @param  AlbumEntry  $album The album entry to 
     *          insert.
     * @param string $url (optional) The URI that the album should be 
     *          uploaded to. If null, the default album creation URI \for 
     *          this domain will be used.
     * @return  AlbumEntry  The inserted album entry as 
     *          returned by the server.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function insertAlbumEntry($album, $uri = null)
    {
        if ($uri === null) {
            $uri = self::PICASA_BASE_FEED_URI . '/' .
                self::DEFAULT_PROJECTION . '/' . self::USER_PATH . '/' .
                self::DEFAULT_USER;
        }
        $newEntry = $this->insertEntry($album, $uri, '\Zend\Gdata\Photos\AlbumEntry');
        return $newEntry;
    }

    /**
     * Create a new photo from a PhotoEntry.
     * 
     * @param  PhotoEntry  $photo The photo to insert.
     * @param string $url The URI that the photo should be uploaded 
     *          to. Alternatively, an AlbumEntry can be provided and the 
     *          photo will be added to that album.
     * @return  PhotoEntry  The inserted photo entry 
     *          as returned by the server.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function insertPhotoEntry($photo, $uri = null)
    {
        if ($uri instanceof  AlbumEntry ) {
            $uri = $uri->getLink(self::FEED_LINK_PATH)->href;
        }
        if ($uri === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'URI must not be null');
        }
        $newEntry = $this->insertEntry($photo, $uri, '\Zend\Gdata\Photos\PhotoEntry');
        return $newEntry;
    }
    
    /**
     * Create a new tag from a TagEntry.
     * 
     * @param  TagEntry  $tag The tag entry to insert.
     * @param string $url The URI where the tag should be 
     *          uploaded to. Alternatively, a PhotoEntry can be provided and 
     *          the tag will be added to that photo.
     * @return  TagEntry  The inserted tag entry as returned
     *          by the server.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function insertTagEntry($tag, $uri = null)
    {
        if ($uri instanceof  PhotoEntry ) {
            $uri = $uri->getLink(self::FEED_LINK_PATH)->href;
        }
        if ($uri === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'URI must not be null');
        }
        $newEntry = $this->insertEntry($tag, $uri, '\Zend\Gdata\Photos\TagEntry');
        return $newEntry;
    }

    /**
     * Create a new comment from a CommentEntry.
     * 
     * @param  CommentEntry  $comment The comment entry to
     *          insert.
     * @param string $url The URI where the comment should be uploaded to.
     *          Alternatively, a PhotoEntry can be provided and 
     *          the comment will be added to that photo.
     * @return  CommentEntry  The inserted comment entry
     *          as returned by the server.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function insertCommentEntry($comment, $uri = null)
    {
        if ($uri instanceof  PhotoEntry ) {
            $uri = $uri->getLink(self::FEED_LINK_PATH)->href;
        }
        if ($uri === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'URI must not be null');
        }
        $newEntry = $this->insertEntry($comment, $uri, '\Zend\Gdata\Photos\CommentEntry');
        return $newEntry;
    }

    /**
     * Delete an AlbumEntry.
     * 
     * @param  AlbumEntry  $album The album entry to 
     *          delete.
     * @param boolean $catch Whether to catch an exception when
     *            modified and re-delete or throw
     * @return void.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function deleteAlbumEntry($album, $catch)
    {
        if ($catch) {
            try {
                $this->delete($album);
            } catch ( HttpException  $e) {
                if ($e->getResponse()->getStatus() === 409) {
                    $entry = new  AlbumEntry ($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($album);
        }
    }

    /**
     * Delete a PhotoEntry.
     * 
     * @param  PhotoEntry  $photo The photo entry to 
     *          delete.
     * @param boolean $catch Whether to catch an exception when
     *            modified and re-delete or throw
     * @return void.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function deletePhotoEntry($photo, $catch)
    {
        if ($catch) {
            try {
                $this->delete($photo);
            } catch ( HttpException  $e) {
                if ($e->getResponse()->getStatus() === 409) {
                    $entry = new  PhotoEntry ($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($photo);
        }
    }
    
    /**
     * Delete a CommentEntry.
     * 
     * @param  CommentEntry  $comment The comment entry to 
     *          delete.
     * @param boolean $catch Whether to catch an exception when
     *            modified and re-delete or throw
     * @return void.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function deleteCommentEntry($comment, $catch)
    {
        if ($catch) {
            try {
                $this->delete($comment);
            } catch ( HttpException  $e) {
                if ($e->getResponse()->getStatus() === 409) {
                    $entry = new  CommentEntry ($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($comment);
        }
    }

    /**
     * Delete a TagEntry.
     * 
     * @param  TagEntry  $tag The tag entry to 
     *          delete.
     * @param boolean $catch Whether to catch an exception when
     *            modified and re-delete or throw
     * @return void.
     * @throws  Gdata _App_Exception
     * @throws  HttpException 
     */
    public function deleteTagEntry($tag, $catch)
    {
        if ($catch) {
            try {
                $this->delete($tag);
            } catch ( HttpException  $e) {
                if ($e->getResponse()->getStatus() === 409) {
                    $entry = new  TagEntry ($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($tag);
        }
    }

}
