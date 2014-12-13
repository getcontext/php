<?php

namespace Zend;



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
 * @package     Feed 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Feed.php 10387 2008-07-24 20:14:26Z matthew $
 */


/**
 * Feed utility class
 *
 * Base  Feed  class, containing constants and the  Client  instance
 * accessor.
 *
 * @category   Zend
 * @package     Feed 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Feed\Builder\BuilderInterface as FeedBuilderInterface;
use Zend\Feed\Exception as FeedException;
use Zend\Uri\Exception as UriException;
use Zend\Feed\FeedAbstract as FeedAbstract;
use Zend\Feed\Builder as Builder;
use Zend\Http\Client as Client;
use Zend\Feed\Atom as Atom;
use Zend\Feed\Rss as Rss;
use Zend\Loader as Loader;
use Zend\Uri as Uri;




class  Feed 
{

    /**
     * HTTP client object to use \for retrieving feeds
     *
     * @var  Client 
     */
    protected static $_httpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    protected static $_httpMethodOverride = false;

    /**
     * @var array
     */
    protected static $_namespaces = array(
        'opensearch' => 'http://a9.com/-/spec/opensearchrss/1.0/',
        'atom'       => 'http://www.w3.org/2005/Atom',
        'rss'        => 'http://blogs.law.harvard.edu/tech/rss',
    );


    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use \for retrieving the feeds.
     *
     * @param   Client  $httpClient
     * @return void
     */
    public static function setHttpClient( Client  $httpClient)
    {
        self::$_httpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object. If none is set, a new  Client  will be used.
     *
     * @return  Client _Abstract
     */
    public static function getHttpClient()
    {
        if (!self::$_httpClient instanceof  Client ) {
            /**
             * @see  Client 
             */
            require_once 'Zend/Http/Client.php';
            self::$_httpClient = new  Client ();
        }

        return self::$_httpClient;
    }


    /**
     * Toggle using POST instead \of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because \of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value \of PUT or
     * DELETE as appropriate.
     *
     * @param  boolean $override Whether to override PUT and DELETE.
     * @return void
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
    }


    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }


    /**
     * Get the full version \of a namespace prefix
     *
     * Looks up a prefix (atom:, etc.) in the list \of registered
     * namespaces and returns the full namespace URI if
     * available. Returns the prefix, unmodified, if it's not
     * registered.
     *
     * @return string
     */
    public static function lookupNamespace($prefix)
    {
        return isset(self::$_namespaces[$prefix]) ?
            self::$_namespaces[$prefix] :
            $prefix;
    }


    /**
     * Add a namespace and prefix to the registered list
     *
     * Takes a prefix and a full namespace URI and adds them to the
     * list \of registered namespaces \for use by
     *  Feed ::lookupNamespace().
     *
     * @param  string $prefix The namespace prefix
     * @param  string $namespaceURI The full namespace URI
     * @return void
     */
    public static function registerNamespace($prefix, $namespaceURI)
    {
        self::$_namespaces[$prefix] = $namespaceURI;
    }


    /**
     * Imports a feed located at $uri.
     *
     * @param  string $uri
     * @throws  FeedException 
     * @return  FeedAbstract 
     */
    public static function import($uri)
    {
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->request('GET');
        if ($response->getStatus() !== 200) {
            /**
             * @see  FeedException 
             */
            require_once 'Zend/Feed/Exception.php';
            throw new  FeedException ('Feed failed to load, got response code ' . $response->getStatus());
        }
        $feed = $response->getBody();
        return self::importString($feed);
    }


    /**
     * Imports a feed represented by $string.
     *
     * @param  string $string
     * @throws  FeedException 
     * @return  FeedAbstract 
     */
    public static function importString($string)
    {
        // Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument;
        $status = @$doc->loadXML($string);
        @ini_restore('track_errors');

        if (!$status) {
            // prevent the class to generate an undefined variable notice (ZF-2590)
            if (!isset($php_errormsg)) {
                if (function_exists('xdebug_is_enabled')) {
                    $php_errormsg = '(error message not available, when XDebug is running)';
                } else {
                    $php_errormsg = '(error message not available)';
                }
            }

            /**
             * @see  FeedException 
             */
            require_once 'Zend/Feed/Exception.php';
            throw new  FeedException ("DOMDocument cannot parse XML: $php_errormsg");
        }

        // Try to find the base feed element or a single <entry> \of an Atom feed
        if ($doc->getElementsByTagName('feed')->item(0) ||
            $doc->getElementsByTagName('entry')->item(0)) {
            /**
             * @see  Atom 
             */
            require_once 'Zend/Feed/Atom.php';
            // return a newly created  Atom  object
            return new  Atom (null, $string);
        }

        // Try to find the base feed element \of an RSS feed
        if ($doc->getElementsByTagName('channel')->item(0)) {
            /**
             * @see  Rss 
             */
            require_once 'Zend/Feed/Rss.php';
            // return a newly created  Rss  object
            return new  Rss (null, $string);
        }

        // $string does not appear to be a valid feed \of the supported types
        /**
         * @see  FeedException 
         */
        require_once 'Zend/Feed/Exception.php';
        throw new  FeedException ('Invalid or unsupported feed format');
    }


    /**
     * Imports a feed from a file located at $filename.
     *
     * @param  string $filename
     * @throws  FeedException 
     * @return  FeedAbstract 
     */
    public static function importFile($filename)
    {
        @ini_set('track_errors', 1);
        $feed = @file_get_contents($filename);
        @ini_restore('track_errors');
        if ($feed === false) {
            /**
             * @see  FeedException 
             */
            require_once 'Zend/Feed/Exception.php';
            throw new  FeedException ("File could not be loaded: $php_errormsg");
        }
        return self::importString($feed);
    }


    /**
     * Attempts to find feeds at $uri referenced by <link ... /> tags. Returns an
     * array \of the feeds referenced at $uri.
     *
     * @todo Allow findFeeds() to follow one, but only one, code 302.
     *
     * @param  string $uri
     * @throws  FeedException 
     * @return array
     */
    public static function findFeeds($uri)
    {
        // Get the HTTP response from $uri and save the contents
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->request();
        if ($response->getStatus() !== 200) {
            /**
             * @see  FeedException 
             */
            require_once 'Zend/Feed/Exception.php';
            throw new  FeedException ("Failed to access $uri, got response code " . $response->getStatus());
        }
        $contents = $response->getBody();

        // Parse the contents \for appropriate <link ... /> tags
        @ini_set('track_errors', 1);
        $pattern = '~(<link[^>]+)/?>~i';
        $result = @preg_match_all($pattern, $contents, $matches);
        @ini_restore('track_errors');
        if ($result === false) {
            /**
             * @see  FeedException 
             */
            require_once 'Zend/Feed/Exception.php';
            throw new  FeedException ("Internal error: $php_errormsg");
        }

        // Try to fetch a feed \for each link tag that appears to refer to a feed
        $feeds = array();
        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $link) {
                // force string to be an utf-8 one
                if (!mb_check_encoding($link, 'UTF-8')) {
                    $link = mb_convert_encoding($link, 'UTF-8');
                }
                $xml = @simplexml_load_string(rtrim($link, ' /') . ' />');
                if ($xml === false) {
                    continue;
                }
                $attributes = $xml->attributes();
                if (!isset($attributes['rel']) || !@preg_match('~^(?:alternate|service\.feed)~i', $attributes['rel'])) {
                    continue;
                }
                if (!isset($attributes['type']) ||
                        !@preg_match('~^application/(?:atom|rss|rdf)\+xml~', $attributes['type'])) {
                    continue;
                }
                if (!isset($attributes['href'])) {
                    continue;
                }
                try {
                    // checks if we need to canonize the given uri
                    try {
                        $uri =  Uri ::factory((string) $attributes['href']);
                    } catch ( UriException  $e) {
                        // canonize the uri
                        $path = (string) $attributes['href'];
                        $query = $fragment = '';
                        if (substr($path, 0, 1) != '/') {
                            // add the current root path to this one
                            $path = rtrim($client->getUri()->getPath(), '/') . '/' . $path;
                        }
                        if (strpos($path, '?') !== false) {
                            list($path, $query) = explode('?', $path, 2);
                        }
                        if (strpos($query, '#') !== false) {
                            list($query, $fragment) = explode('#', $query, 2);
                        }
                        $uri =  Uri ::factory($client->getUri(true));
                        $uri->setPath($path);
                        $uri->setQuery($query);
                        $uri->setFragment($fragment);
                    }

                    $feed = self::import($uri);
                } catch (\Exception $e) {
                    continue;
                }
                $feeds[] = $feed;
            }
        }

        // Return the fetched feeds
        return $feeds;
    }

    /**
     * Construct a new  FeedAbstract  object from a custom array
     *
     * @param  array  $data
     * @param  string $format (rss|atom) the requested output format
     * @return  FeedAbstract 
     */
    public static function importArray(array $data, $format = 'atom')
    {
        $obj = '\Zend\Feed_' . ucfirst(strtolower($format));
        /**
         * @see  Loader 
         */
        require_once 'Zend/Loader.php';
         Loader ::loadClass($obj);
         Loader ::loadClass('\Zend\Feed\Builder');

        return new $obj(null, null, new  Builder ($data));
    }

    /**
     * Construct a new  FeedAbstract  object from a  FeedBuilderInterface  data source
     *
     * @param   FeedBuilderInterface  $builder this object will be used to extract the data \of the feed
     * @param  string                      $format (rss|atom) the requested output format
     * @return  FeedAbstract 
     */
    public static function importBuilder( FeedBuilderInterface  $builder, $format = 'atom')
    {
        $obj = '\Zend\Feed_' . ucfirst(strtolower($format));
        /**
         * @see  Loader 
         */
        require_once 'Zend/Loader.php';
         Loader ::loadClass($obj);

        return new $obj(null, null, $builder);
    }
}
