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
 * @see  Photos _UserEntry
 */
require_once 'Zend/Gdata/Photos/UserEntry.php';

/**
 * @see  Photos _AlbumEntry
 */
require_once 'Zend/Gdata/Photos/AlbumEntry.php';

/**
 * @see  Photos _PhotoEntry
 */
require_once 'Zend/Gdata/Photos/PhotoEntry.php';

/**
 * @see  Photos _TagEntry
 */
require_once 'Zend/Gdata/Photos/TagEntry.php';

/**
 * @see  Photos _CommentEntry
 */
require_once 'Zend/Gdata/Photos/CommentEntry.php';

/**
 * \Data model \for a collection \of entries \for a specific user, usually 
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


use Zend\Gdata\Photos\Extension\Thumbnail as Thumbnail;
use Zend\Gdata\Photos\Extension\Nickname as Nickname;
use Zend\Gdata\Photos\Extension\User as GdataPhotosExtensionUser;
use Zend\Gdata\App\Exception as GdataAppException;
use Zend\Gdata\App\Entry as Entry;
use Zend\Gdata\Photos as Photos;
use Zend\Gdata\Feed as Feed;




class  UserFeed  extends  Feed 
{
    
    /**
     * gphoto:user element
     *
     * @var  GdataPhotosExtensionUser 
     */
    protected $_gphotoUser = null;
    
    /**
     * gphoto:thumbnail element
     *
     * @var  Thumbnail 
     */
    protected $_gphotoThumbnail = null;
    
    /**
     * gphoto:nickname element
     *
     * @var  Nickname 
     */
    protected $_gphotoNickname = null;

    protected $_entryClassName = '\Zend\Gdata\Photos\UserEntry';
    protected $_feedClassName = '\Zend\Gdata\Photos\UserFeed';

    protected $_entryKindClassMapping = array(
        'http://schemas.google.com/photos/2007#album' => '\Zend\Gdata\Photos\AlbumEntry',
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
    
    /**
     * Creates individual Entry objects \of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
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
            case $this->lookupNamespace('gphoto') . ':' . 'thumbnail';
                $thumbnail = new  Thumbnail ();
                $thumbnail->transferFromDOM($child);
                $this->_gphotoThumbnail = $thumbnail;
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

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_gphotoUser != null) {
            $element->appendChild($this->_gphotoUser->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoNickname != null) {
            $element->appendChild($this->_gphotoNickname->getDOM($element->ownerDocument));
        }
        if ($this->_gphotoThumbnail != null) {
            $element->appendChild($this->_gphotoThumbnail->getDOM($element->ownerDocument));
        }

        return $element;
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
     * Get the value \for this element's gphoto:thumbnail attribute.
     *
     * @see setGphotoThumbnail
     * @return string The requested attribute.
     */
    public function getGphotoThumbnail()
    {
        return $this->_gphotoThumbnail;
    }
    
    /**
     * Set the value \for this element's gphoto:thumbnail attribute.
     *
     * @param string $value The desired value \for this attribute.
     * @return  Thumbnail  The element being modified.
     */
    public function setGphotoThumbnail($value)
    {
        $this->_gphotoThumbnail = $value;
        return $this;
    }
    
}
