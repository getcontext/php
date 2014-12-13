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
 * @see  Gbase _ItemFeed
 */
require_once 'Zend/Gdata/Gbase/ItemFeed.php';

/**
 * @see  ItemEntry 
 */
require_once 'Zend/Gdata/Gbase/ItemEntry.php';

/**
 * @see  Gbase _SnippetEntry
 */
require_once 'Zend/Gdata/Gbase/SnippetEntry.php';

/**
 * @see  Gbase _SnippetFeed
 */
require_once 'Zend/Gdata/Gbase/SnippetFeed.php';

/**
 * Service class \for interacting with the Google Base data API
 *
 * @link http://code.google.com/apis/base
 *
 * @category   Zend
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\InvalidArgumentException as GdataAppInvalidArgumentException;
use Zend\Gdata\Gbase\ItemEntry as ItemEntry;
use Zend\Http\Client as Client;
use Zend\Gdata\Query as Query;
use Zend\Gdata as Gdata;




class  Gbase  extends  Gdata 
{

    /**
     * Path to the customer items feeds on the Google Base server.
     */
    const GBASE_ITEM_FEED_URI = 'http://www.google.com/base/feeds/items';

    /**
     * Path to the snippets feeds on the Google Base server.
     */
    const GBASE_SNIPPET_FEED_URI = 'http://www.google.com/base/feeds/snippets';

    /**
     * Authentication service name \for Google Base
     */
    const AUTH_SERVICE_NAME = 'gbase';

    /**
     * The default URI \for POST methods
     *
     * @var string
     */    
    protected $_defaultPostUri = self::GBASE_ITEM_FEED_URI;

    public static $namespaces = array(
            'g' => 'http://base.google.com/ns/1.0',
            'batch' => 'http://schemas.google.com/gdata/batch');

    /**
     * Create  Gbase  object
     *
     * @param  Client  $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $applicationId The identity \of the app in the form \of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('\Zend\Gdata\Gbase');
        $this->registerPackage('\Zend\Gdata\Gbase_Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retreive feed object
     *
     * @param mixed $location The location \for the feed, as a URL or Query
     * @return  Gbase _ItemFeed
     */
    public function getGbaseItemFeed($location = null)
    {
        if ($location === null) {
            $uri = self::GBASE_ITEM_FEED_URI;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\Gbase\ItemFeed');
    }

    /**
     * Retreive entry object
     *
     * @param mixed $location The location \for the feed, as a URL or Query
     * @return  ItemEntry 
     */
    public function getGbaseItemEntry($location = null)
    {
        if ($location === null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Gbase\ItemEntry');
    }

    /**
     * Insert an entry
     *
     * @param  ItemEntry  $entry The Base entry to upload
     * @param boolean $dryRun Flag \for the 'dry-run' parameter
     * @return  Gbase _ItemFeed
     */
    public function insertGbaseItem($entry, $dryRun = false)
    {
        if ($dryRun == false) {
            $uri = $this->_defaultPostUri;
        } else {
            $uri = $this->_defaultPostUri . '?dry-run=true';
        }
        $newitem = $this->insertEntry($entry, $uri, '\Zend\Gdata\Gbase\ItemEntry');
        return $newitem;
    }

    /**
     * Update an entry
     *
     * @param  ItemEntry  $entry The Base entry to be updated
     * @param boolean $dryRun Flag \for the 'dry-run' parameter
     * @return  ItemEntry 
     */
    public function updateGbaseItem($entry, $dryRun = false)
    {
        $returnedEntry = $entry->save($dryRun);
        return $returnedEntry;
    }

    /**
     * Delete an entry
     *
     * @param  ItemEntry  $entry The Base entry to remove
     * @param boolean $dryRun Flag \for the 'dry-run' parameter
     * @return  Gbase _ItemFeed
     */
    public function deleteGbaseItem($entry, $dryRun = false)
    {
        $entry->delete($dryRun);
        return $this;
    }

    /**
     * Retrieve feed object
     *
     * @param mixed $location The location \for the feed, as a URL or Query
     * @return  Gbase _SnippetFeed
     */
    public function getGbaseSnippetFeed($location = null)
    {
        if ($location === null) {
            $uri = self::GBASE_SNIPPET_FEED_URI;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\Gbase\SnippetFeed');
    }
}
