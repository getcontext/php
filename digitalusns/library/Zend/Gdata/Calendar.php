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
 * @see  Calendar _EventFeed
 */
require_once 'Zend/Gdata/Calendar/EventFeed.php';

/**
 * @see  Calendar _EventEntry
 */
require_once 'Zend/Gdata/Calendar/EventEntry.php';

/**
 * @see  Calendar _ListFeed
 */
require_once 'Zend/Gdata/Calendar/ListFeed.php';

/**
 * @see  Calendar _ListEntry
 */
require_once 'Zend/Gdata/Calendar/ListEntry.php';

/**
 * Service class \for interacting with the Google Calendar data API
 * @link http://code.google.com/apis/gdata/calendar.html
 *
 * @category   Zend
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\InvalidArgumentException as GdataAppInvalidArgumentException;
use Zend\Http\Client as Client;
use Zend\Gdata\Query as Query;
use Zend\Gdata as Gdata;




class  Calendar  extends  Gdata 
{

    const CALENDAR_FEED_URI = 'http://www.google.com/calendar/feeds';
    const CALENDAR_EVENT_FEED_URI = 'http://www.google.com/calendar/feeds/default/private/full';
    const AUTH_SERVICE_NAME = 'cl';

    protected $_defaultPostUri = self::CALENDAR_EVENT_FEED_URI;

    public static $namespaces = array(
            'gCal' => 'http://schemas.google.com/gCal/2005');

    /**
     * Create Gdata_Calendar object
     *
     * @param  Client  $client (optional) The HTTP client to use when
     *          when communicating with the Google servers.
     * @param string $applicationId The identity \of the app in the form \of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('\Zend\Gdata\Calendar');
        $this->registerPackage('\Zend\Gdata\Calendar_Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retreive feed object
     *
     * @param mixed $location The location \for the feed, as a URL or Query
     * @return  Calendar _EventFeed
     */
    public function getCalendarEventFeed($location = null)
    {
        if ($location == null) {
            $uri = self::CALENDAR_EVENT_FEED_URI;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\Calendar\EventFeed');
    }

    /**
     * Retreive entry object
     *
     * @return  Calendar _EventEntry
     */
    public function getCalendarEventEntry($location = null)
    {
        if ($location == null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\Calendar\EventEntry');
    }


    /**
     * Retrieve feed object
     *
     * @return  Calendar _ListFeed
     */
    public function getCalendarListFeed()
    {
        $uri = self::CALENDAR_FEED_URI . '/default';
        return parent::getFeed($uri,'\Zend\Gdata\Calendar\ListFeed');
    }

    /**
     * Retreive entryobject
     *
     * @return  Calendar _ListEntry
     */
    public function getCalendarListEntry($location = null)
    {
        if ($location == null) {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new  GdataAppInvalidArgumentException (
                    'Location must not be null');
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri,'\Zend\Gdata\Calendar\ListEntry');
    }

    public function insertEvent($event, $uri=null)
    {
        if ($uri == null) {
            $uri = $this->_defaultPostUri;
        }
        $newEvent = $this->insertEntry($event, $uri, '\Zend\Gdata\Calendar\EventEntry');
        return $newEvent;
    }

}
