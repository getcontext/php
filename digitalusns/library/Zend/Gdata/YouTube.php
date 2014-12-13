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
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  Media 
 */
require_once 'Zend/Gdata/Media.php';

/**
 * @see  VideoEntry 
 */
require_once 'Zend/Gdata/YouTube/VideoEntry.php';

/**
 * @see  YouTube _VideoFeed
 */
require_once 'Zend/Gdata/YouTube/VideoFeed.php';

/**
 * @see  YouTube _CommentFeed
 */
require_once 'Zend/Gdata/YouTube/CommentFeed.php';

/**
 * @see  YouTube _PlaylistListFeed
 */
require_once 'Zend/Gdata/YouTube/PlaylistListFeed.php';

/**
 * @see  YouTube _SubscriptionFeed
 */
require_once 'Zend/Gdata/YouTube/SubscriptionFeed.php';

/**
 * @see  YouTube _ContactFeed
 */
require_once 'Zend/Gdata/YouTube/ContactFeed.php';

/**
 * @see  YouTube _PlaylistVideoFeed
 */
require_once 'Zend/Gdata/YouTube/PlaylistVideoFeed.php';

/**
 * Service class \for interacting with the services which use the media extensions
 * @link http://code.google.com/apis/gdata/calendar.html
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\YouTube\VideoEntry as VideoEntry;
use Zend\Gdata\YouTube\VideoQuery as VideoQuery;
use Zend\Gdata\App\HttpException as HttpException;
use Zend\Gdata\App\Exception as GdataAppException;
use Zend\Gdata\Media as Media;
use Zend\Http\Client as Client;
use Zend\Gdata\Query as Query;




class  YouTube  extends  Media 
{

    const AUTH_SERVICE_NAME = 'youtube';
    const CLIENTLOGIN_URL = 'https://www.google.com/youtube/accounts/ClientLogin';

    const STANDARD_TOP_RATED_URI = 'http://gdata.youtube.com/feeds/standardfeeds/top_rated';
    const STANDARD_MOST_VIEWED_URI = 'http://gdata.youtube.com/feeds/standardfeeds/most_viewed';
    const STANDARD_RECENTLY_FEATURED_URI = 'http://gdata.youtube.com/feeds/standardfeeds/recently_featured';
    const STANDARD_WATCH_ON_MOBILE_URI = 'http://gdata.youtube.com/feeds/standardfeeds/watch_on_mobile';

    const VIDEO_URI = 'http://gdata.youtube.com/feeds/videos';
    const PLAYLIST_REL = 'http://gdata.youtube.com/schemas/2007#playlist';
    const USER_UPLOADS_REL = 'http://gdata.youtube.com/schemas/2007#user.uploads';
    const USER_PLAYLISTS_REL = 'http://gdata.youtube.com/schemas/2007#user.playlists';
    const USER_SUBSCRIPTIONS_REL = 'http://gdata.youtube.com/schemas/2007#user.subscriptions';
    const USER_CONTACTS_REL = 'http://gdata.youtube.com/schemas/2007#user.contacts';
    const USER_FAVORITES_REL = 'http://gdata.youtube.com/schemas/2007#user.favorites';
    const VIDEO_RESPONSES_REL = 'http://gdata.youtube.com/schemas/2007#video.responses';
    const VIDEO_RATINGS_REL = 'http://gdata.youtube.com/schemas/2007#video.ratings';
    const VIDEO_COMPLAINTS_REL = 'http://gdata.youtube.com/schemas/2007#video.complaints';

    const FAVORITES_URI_SUFFIX = 'favorites';
    const UPLOADS_URI_SUFFIX = 'uploads';
    const RESPONSES_URI_SUFFIX = 'responses';
    const RELATED_URI_SUFFIX = 'related';
    
    public static $namespaces = array(
            'yt' => 'http://gdata.youtube.com/schemas/2007',
            'georss' => 'http://www.georss.org/georss',
            'gml' => 'http://www.opengis.net/gml',
            'media' => 'http://search.yahoo.com/mrss/',
            'app' => 'http://purl.org/atom/app#');

    /**
     * Create  YouTube  object
     *
     * @param  Client  $client (optional) The HTTP client to use when
     *          when communicating with the Google servers.
     * @param string $applicationId The identity \of the app in the form \of Company-AppName-Version
     * @param string $clientId The clientId issued by the YouTube dashboard
     * @param string $developerKey The developerKey issued by the YouTube dashboard
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0', $clientId = null, $developerKey = null)
    {
        $this->registerPackage('\Zend\Gdata\YouTube');
        $this->registerPackage('\Zend\Gdata\YouTube_Extension');
        $this->registerPackage('\Zend\Gdata\Media');
        $this->registerPackage('\Zend\Gdata\Media_Extension');


        // NOTE This constructor no longer calls the parent constructor
        $this->setHttpClient($client, $applicationId, $clientId, $developerKey);
    }

    /**
     * Set the  Client  object used \for communication
     *
     * @param  Client  $client The client to use \for communication
     * @throws  HttpException 
     * @return \Zend\Gdata\App Provides a fluent interface
     */
    public function setHttpClient($client, $applicationId = 'MyCompany-MyApp-1.0', $clientId = null, $developerKey = null)
    {
        if ($client === null) {
            $client = new  Client ();
        }
        if (!$client instanceof  Client ) {
            require_once 'Zend/Gdata/App/HttpException.php';
            throw new  HttpException ('Argument is not an instance \of  Client .');
        }

        if ($clientId != null) {
            $client->setHeaders('X-GData-Client', $clientId);
        }

        if ($developerKey != null) {
            $client->setHeaders('X-GData-Key', 'key='. $developerKey);
        }

        return parent::setHttpClient($client, $applicationId);
    }

    /**
     * Retrieves a feed \of videos.
     *
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getVideoFeed($location = null)
    {
        if ($location == null) {
            $uri = self::VIDEO_URI;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a specific video entry.
     *
     * @param mixed $videoId The videoId \of interest
     *          Query  object from which a URL can be determined
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  VideoEntry  The feed \of videos found at the 
     *         specified URL.
     */
    public function getVideoEntry($videoId = null, $location = null)
    {
        if ($videoId !== null) {
            $uri = self::VIDEO_URI . "/" . $videoId;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\YouTube\VideoEntry');
    }

    /**
     * Retrieves a feed \of videos related to the specified video ID.
     *
     * @param string $videoId The videoId \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getRelatedVideoFeed($videoId = null, $location = null)
    {
        if ($videoId !== null) {
            $uri = self::VIDEO_URI . "/" . $videoId . "/" . self::RELATED_URI_SUFFIX;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a feed \of video responses related to the specified video ID.
     *
     * @param string $videoId The videoId \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getVideoResponseFeed($videoId = null, $location = null)
    {
        if ($videoId !== null) {
            $uri = self::VIDEO_URI . "/" . $videoId . "/" . self::RESPONSES_URI_SUFFIX;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a feed \of comments related to the specified video ID.
     *
     * @param string $videoId The videoId \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _CommentFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getVideoCommentFeed($videoId = null, $location = null)
    {
        if ($videoId !== null) {
            $uri = self::VIDEO_URI . "/" . $videoId . "/comments";
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\CommentFeed');
    }

    /**
     * Retrieves a feed \of comments related to the specified video ID.
     *
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _CommentFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getTopRatedVideoFeed($location = null)
    {
        if ($location == null) {
            $uri = self::STANDARD_TOP_RATED_URI;
        } else if ($location instanceof  Query ) {
            if ($location instanceof  VideoQuery ) {
                if (!isset($location->url)) {
                    $location->setFeedType('top rated');
                }
            }
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    
    /**
     * Retrieves a feed \of the most viewed videos.
     *
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getMostViewedVideoFeed($location = null)
    {
        if ($location == null) {
            $uri = self::STANDARD_MOST_VIEWED_URI;
        } else if ($location instanceof  Query ) {
            if ($location instanceof  VideoQuery ) {
                if (!isset($location->url)) {
                    $location->setFeedType('most viewed');
                }
            }
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a feed \of recently featured videos.
     *
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getRecentlyFeaturedVideoFeed($location = null)
    {
        if ($location == null) {
            $uri = self::STANDARD_RECENTLY_FEATURED_URI;
        } else if ($location instanceof  Query ) {
            if ($location instanceof  VideoQuery ) {
                if (!isset($location->url)) {
                    $location->setFeedType('recently featured');
                }
            }
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a feed \of videos recently featured \for mobile devices.
     * These videos will have RTSP links in the $entry->mediaGroup->content
     *
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The feed \of videos found at the 
     *         specified URL.
     */
    public function getWatchOnMobileVideoFeed($location = null)
    {
        if ($location == null) {
            $uri = self::STANDARD_WATCH_ON_MOBILE_URI;
        } else if ($location instanceof  Query ) {
            if ($location instanceof  VideoQuery ) {
                if (!isset($location->url)) {
                    $location->setFeedType('watch on mobile');
                }
            }
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a feed which lists a user's playlist
     *
     * @param string $user (optional) The username \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _PlaylistListFeed The feed \of playlists 
     */
    public function getPlaylistListFeed($user = null, $location = null)
    {
        if ($user !== null) {
            $uri = 'http://gdata.youtube.com/feeds/users/' . $user . '/playlists';
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\PlaylistListFeed');
    }

    /**
     * Retrieves a feed \of videos in a particular playlist
     *
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _PlaylistVideoFeed The feed \of videos found at
     *         the specified URL.
     */
    public function getPlaylistVideoFeed($location)
    {
        if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\PlaylistVideoFeed');
    }

    /**
     * Retrieves a feed \of a user's subscriptions
     *
     * @param string $user (optional) The username \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _SubscriptionListFeed The feed \of subscriptions 
     */
    public function getSubscriptionFeed($user = null, $location = null)
    {
        if ($user !== null) {
            $uri = 'http://gdata.youtube.com/feeds/users/' . $user . '/subscriptions';
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\SubscriptionFeed');
    }

    /**
     * Retrieves a feed \of a user's contacts
     *
     * @param string $user (optional) The username \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _ContactFeed The feed \of contacts 
     */
    public function getContactFeed($user = null, $location = null)
    {
        if ($user !== null) {
            $uri = 'http://gdata.youtube.com/feeds/users/' . $user . '/contacts';
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\ContactFeed');
    }

    /**
     * Retrieves a user's uploads
     *
     * @param string $user (optional) The username \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The videos uploaded by the user
     */
    public function getUserUploads($user = null, $location = null)
    {
        if ($user !== null) {
            $uri = 'http://gdata.youtube.com/feeds/users/' . $user . '/' .
                   self::UPLOADS_URI_SUFFIX;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a user's favorites
     *
     * @param string $user (optional) The username \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _VideoFeed The videos favorited by the user
     */
    public function getUserFavorites($user = null, $location = null)
    {
        if ($user !== null) {
            $uri = 'http://gdata.youtube.com/feeds/users/' . $user . '/' .
                   self::FAVORITES_URI_SUFFIX;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, '\Zend\Gdata\YouTube\VideoFeed');
    }

    /**
     * Retrieves a user's profile as an entry 
     *
     * @param string $user (optional) The username \of interest
     * @param mixed $location (optional) The URL to query or a
     *          Query  object from which a URL can be determined
     * @return  YouTube _UserProfileEntry The user profile entry
     */
    public function getUserProfile($user = null, $location = null)
    {
        if ($user !== null) {
            $uri = 'http://gdata.youtube.com/feeds/users/' . $user;
        } else if ($location instanceof  Query ) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, '\Zend\Gdata\YouTube\UserProfileEntry');
    }

    /**
     * Helper function \for parsing a YouTube token response
     * 
     * @param string $response The service response
     * @return array An array containing the token and URL
     */
    public static function parseFormUploadTokenResponse($response)
    {
        // Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($response);
        @ini_restore('track_errors');

        if (!$success) {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new  GdataAppException ("\Zend\Gdata\YouTube::parseFormUploadTokenResponse - " .
                                               "DOMDocument cannot parse XML: $php_errormsg");
        }
        $responseElement = $doc->getElementsByTagName('response')->item(0);

        $urlText = null;
        $tokenText = null;
        if ($responseElement != null) {
            $urlElement = $responseElement->getElementsByTagName('url')->item(0);
            $tokenElement = $responseElement->getElementsByTagName('token')->item(0);

            if ($urlElement && $urlElement->hasChildNodes() &&
                $tokenElement && $tokenElement->hasChildNodes()) {

                $urlText = $urlElement->firstChild->nodeValue;
                $tokenText = $tokenElement->firstChild->nodeValue;
            }
        }

        if ($tokenText != null && $urlText != null) {
            return array('token' => $tokenText, 'url' => $urlText);
        } else {
            require_once 'Zend/Gdata/App/Exception.php';
            throw new  GdataAppException ("form upload token not found in response");
        }
    }

    /**
     * Retrieves a YouTube token
     * 
     * @param  VideoEntry  $videoEntry The video entry
     * @param string $url The location as a string URL
     * @return array An array containing a token and URL
     */
    public function getFormUploadToken($videoEntry, $url='http://gdata.youtube.com/action/GetUploadToken')
    {
        if ($url != null && is_string($url)) {
            // $response is a Zend_Http_response object
            $response = $this->post($videoEntry, $url);
            return self::parseFormUploadTokenResponse($response->getBody()); 
        } else {
            throw new  GdataAppException ('url must be provided as a string URL');
        }
    }

}
