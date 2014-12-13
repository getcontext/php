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
 * @see \Zend\Gdata\App\Extension\Element
*/
require_once 'Zend/Gdata/App/Extension/Element.php';

/**
 * @see  Author 
*/
require_once 'Zend/Gdata/App/Extension/Author.php';

/**
 * @see  Category 
*/
require_once 'Zend/Gdata/App/Extension/Category.php';

/**
 * @see  Contributor 
*/
require_once 'Zend/Gdata/App/Extension/Contributor.php';

/**
 * @see  Id 
 */
require_once 'Zend/Gdata/App/Extension/Id.php';

/**
 * @see  Link 
 */
require_once 'Zend/Gdata/App/Extension/Link.php';

/**
 * @see  Rights 
 */
require_once 'Zend/Gdata/App/Extension/Rights.php';

/**
 * @see  Title 
 */
require_once 'Zend/Gdata/App/Extension/Title.php';

/**
 * @see  Updated 
 */
require_once 'Zend/Gdata/App/Extension/Updated.php';

/**
 *  Version 
 */
require_once 'Zend/Version.php';

/**
 * Abstract class \for common functionality in entries and feeds
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Gdata\App\Extension\Contributor as Contributor;
use Zend\Gdata\App\Extension\Category as Category;
use Zend\Gdata\App\Extension\Updated as Updated;
use Zend\Gdata\App\Extension\Rights as Rights;
use Zend\Gdata\App\Extension\Author as Author;
use Zend\Gdata\App\Extension\Title as Title;
use Zend\Gdata\App\Extension\Link as Link;
use Zend\Gdata\App\Extension\Id as Id;
use Zend\Gdata\App\Exception as GdataAppException;
use Zend\Gdata\App\Base as Base;
use Zend\Http\Client as Client;
use Zend\Version as Version;



abstract class  FeedEntryParent  extends  Base 
{

    /**
     * HTTP client object to use \for retrieving feeds
     *
     * @var  Client 
     */
    protected $_httpClient = null;

    protected $_author = array();
    protected $_category = array();
    protected $_contributor = array();
    protected $_id = null;
    protected $_link = array();
    protected $_rights = null;
    protected $_title = null;
    protected $_updated = null;

    /**
     * Constructs a Feed or Entry
     */
    public function __construct($element = null)
    {
        if (!($element instanceof DOMElement)) {
            if ($element) {
                // Load the feed as an XML DOMDocument object
                @ini_set('track_errors', 1);
                $doc = new DOMDocument();
                $success = @$doc->loadXML($element);
                @ini_restore('track_errors');
                if (!$success) {
                    require_once 'Zend/Gdata/App/Exception.php';
                    throw new  GdataAppException ("DOMDocument cannot parse XML: $php_errormsg");
                }
                $element = $doc->getElementsByTagName($this->_rootElement)->item(0);
                if (!$element) {
                    require_once 'Zend/Gdata/App/Exception.php';
                    throw new  GdataAppException ('No root <' . $this->_rootElement . '> element found, cannot parse feed.');
                }
                $this->transferFromDOM($element);
            }
        } else {
            $this->transferFromDOM($element);
        }
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use \for retrieving the feed.
     *
     * @param   Client  $httpClient
     * @return \Zend\Gdata\App\Feed Provides a fluent interface
     */
    public function setHttpClient( Client  $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }


    /**
     * Gets the HTTP client object. If none is set, a new  Client  will be used.
     *
     * @return  Client _Abstract
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof  Client ) {
            /**
             * @see  Client 
             */
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new  Client ();
            $useragent = 'Zend_Framework_Gdata/' .  Version ::VERSION;
            $this->_httpClient->setConfig(array(
                'strictredirects' => true,
                 'useragent' => $useragent
                )
            );
        }
        return $this->_httpClient;
    }


    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        foreach ($this->_author as $author) {
            $element->appendChild($author->getDOM($element->ownerDocument));
        }
        foreach ($this->_category as $category) {
            $element->appendChild($category->getDOM($element->ownerDocument));
        }
        foreach ($this->_contributor as $contributor) {
            $element->appendChild($contributor->getDOM($element->ownerDocument));
        }
        if ($this->_id != null) {
            $element->appendChild($this->_id->getDOM($element->ownerDocument));
        }
        foreach ($this->_link as $link) {
            $element->appendChild($link->getDOM($element->ownerDocument));
        }
        if ($this->_rights != null) {
            $element->appendChild($this->_rights->getDOM($element->ownerDocument));
        }
        if ($this->_title != null) {
            $element->appendChild($this->_title->getDOM($element->ownerDocument));
        }
        if ($this->_updated != null) {
            $element->appendChild($this->_updated->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'author':
            $author = new  Author ();
            $author->transferFromDOM($child);
            $this->_author[] = $author;
            break;
        case $this->lookupNamespace('atom') . ':' . 'category':
            $category = new  Category ();
            $category->transferFromDOM($child);
            $this->_category[] = $category;
            break;
        case $this->lookupNamespace('atom') . ':' . 'contributor':
            $contributor = new  Contributor ();
            $contributor->transferFromDOM($child);
            $this->_contributor[] = $contributor;
            break;
        case $this->lookupNamespace('atom') . ':' . 'id':
            $id = new  Id ();
            $id->transferFromDOM($child);
            $this->_id = $id;
            break;
        case $this->lookupNamespace('atom') . ':' . 'link':
            $link = new  Link ();
            $link->transferFromDOM($child);
            $this->_link[] = $link;
            break;
        case $this->lookupNamespace('atom') . ':' . 'rights':
            $rights = new  Rights ();
            $rights->transferFromDOM($child);
            $this->_rights = $rights;
            break;
        case $this->lookupNamespace('atom') . ':' . 'title':
            $title = new  Title ();
            $title->transferFromDOM($child);
            $this->_title = $title;
            break;
        case $this->lookupNamespace('atom') . ':' . 'updated':
            $updated = new  Updated ();
            $updated->transferFromDOM($child);
            $this->_updated = $updated;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return  Author 
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Sets the list \of the authors \of this feed/entry.  In an atom feed, each
     * author is represented by an atom:author element
     *
     * @param array $value
     * @return  FeedEntryParent  Provides a fluent interface
     */
    public function setAuthor($value)
    {
        $this->_author = $value;
        return $this;
    }

    /**
     * Returns the array \of categories that classify this feed/entry.  Each
     * category is represented in an atom feed by an atom:category element.
     *
     * @return array Array \of  Category 
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Sets the array \of categories that classify this feed/entry.  Each
     * category is represented in an atom feed by an atom:category element.
     *
     * @param array $value Array \of  Category 
     * @return  FeedEntryParent  Provides a fluent interface
     */
    public function setCategory($value)
    {
        $this->_category = $value;
        return $this;
    }

    /**
     * Returns the array \of contributors to this feed/entry.  Each contributor
     * is represented in an atom feed by an atom:contributor XML element
     *
     * @return array An array \of  Contributor 
     */
    public function getContributor()
    {
        return $this->_contributor;
    }

    /**
     * Sets the array \of contributors to this feed/entry.  Each contributor
     * is represented in an atom feed by an atom:contributor XML element
     *
     * @param array $value
     * @return  FeedEntryParent  Provides a fluent interface
     */
    public function setContributor($value)
    {
        $this->_contributor = $value;
        return $this;
    }

    /**
     * @return  Id 
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param  Id  $value
     * @return  FeedEntryParent  Provides a fluent interface
     */
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    /**
     * Given a particular 'rel' value, this method returns a matching
     *  Link  element.  If the 'rel' value 
     * is not provided, the full array \of  Link  
     * elements is returned.  In an atom feed, each link is represented
     * by an atom:link element.  The 'rel' value passed to this function
     * is the atom:link/@rel attribute.  Example rel values include 'self',
     * 'edit', and 'alternate'.
     *
     * @param string $rel The rel value \of the link to be found.  If null,
     *     the array \of \Zend\Gdata\App\Extension_link elements is returned
     * @return mixed Either a single \Zend\Gdata\App\Extension_link element,
     *     an array \of the same or null is returned depending on the rel value
     *     supplied as the argument to this function
     */
    public function getLink($rel = null)
    {
        if ($rel == null) {
            return $this->_link;
        } else {
            foreach ($this->_link as $link) {
                if ($link->rel == $rel) {
                    return $link;
                }
            }
            return null;
        }
    }

    /**
     * Returns the  Link  element which represents
     * the URL used to edit this resource.  This link is in the atom feed/entry
     * as an atom:link with a rel attribute value \of 'edit'.  
     *
     * @return  Link  The link, or null if not found
     */
    public function getEditLink()
    {
        return $this->getLink('edit');
    }

    /**
     * Returns the  Link  element which represents
     * the URL used to retrieve the next chunk \of results when paging through
     * a feed.  This link is in the atom feed as an atom:link with a 
     * rel attribute value \of 'next'.  
     *
     * @return  Link  The link, or null if not found
     */
    public function getNextLink()
    {
        return $this->getLink('next');
    }

    /**
     * Returns the  Link  element which represents
     * the URL used to retrieve the previous chunk \of results when paging 
     * through a feed.  This link is in the atom feed as an atom:link with a 
     * rel attribute value \of 'previous'.  
     *
     * @return  Link  The link, or null if not found
     */
    public function getPreviousLink()
    {
        return $this->getLink('previous');
    }

    /**
     * @return  Link 
     */
    public function getLicenseLink()
    {
        return $this->getLink('license');
    }

    /**
     * Returns the  Link  element which represents
     * the URL used to retrieve the entry or feed represented by this object
     * This link is in the atom feed/entry as an atom:link with a 
     * rel attribute value \of 'self'.  
     *
     * @return  Link  The link, or null if not found
     */
    public function getSelfLink()
    {
        return $this->getLink('self');
    }

    /**
     * Returns the  Link  element which represents
     * the URL \for an alternate view \of the data represented by this feed or
     * entry.  This alternate view is commonly a user-facing webpage, blog 
     * post, etc.  The MIME type \for the data at the URL is available from the
     * returned  Link  element. 
     * This link is in the atom feed/entry as an atom:link with a 
     * rel attribute value \of 'self'.  
     *
     * @return  Link  The link, or null if not found
     */
    public function getAlternateLink()
    {
        return $this->getLink('alternate');
    }

    /**
     * @param array $value The array \of  Link  elements
     * @return  FeedEntryParent  Provides a fluent interface
     */
    public function setLink($value)
    {
        $this->_link = $value;
        return $this;
    }

    /**
     * @return \Zend\Gdata\AppExtension_Rights
     */
    public function getRights()
    {
        return $this->_rights;
    }

    /**
     * @param  Rights  $value
     * @return  FeedEntryParent  Provides a fluent interface
     */
    public function setRights($value)
    {
        $this->_rights = $value;
        return $this;
    }

    /**
     * Returns the title \of this feed or entry.  The title is an extremely
     * short textual representation \of this resource and is found as
     * an atom:title element in a feed or entry
     *
     * @return  Title 
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Returns a string representation \of the title \of this feed or entry.  
     * The title is an extremely short textual representation \of this 
     * resource and is found as an atom:title element in a feed or entry
     *
     * @return string
     */
    public function getTitleValue()
    {
        if (($titleObj = $this->getTitle()) != null) {
            return $titleObj->getText();
        } else {
            return null;
        }
    }

    /**
     * Returns the title \of this feed or entry.  The title is an extremely
     * short textual representation \of this resource and is found as
     * an atom:title element in a feed or entry
     *
     * @param  Title  $value
     * @return \Zend\Gdata\App\Feed_Entry_Parent Provides a fluent interface
     */
    public function setTitle($value)
    {
        $this->_title = $value;
        return $this;
    }

    /**
     * @return  Updated 
     */
    public function getUpdated()
    {
        return $this->_updated;
    }

    /**
     * @param  Updated  $value
     * @return \Zend\Gdata\App\Feed_Entry_Parent Provides a fluent interface
     */
    public function setUpdated($value)
    {
        $this->_updated = $value;
        return $this;
    }

}
