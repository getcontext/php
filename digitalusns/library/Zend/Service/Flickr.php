<?php

namespace Zend\Service;



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
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Flickr.php 8733 2008-03-10 15:56:48Z jokke $
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Flickr\ResultSet as ResultSet;
use Zend\Service\Flickr\Image as Image;
use Zend\Service\Exception as ServiceException;
use Zend\Validate\Between as Between;
use Zend\Validate\Int as Int;
use Zend\Rest\Client as Client;




class  Flickr 
{
    /**
     * Base URI \for the REST client
     */
    const URI_BASE = 'http://www.flickr.com';

    /**
     * Your Flickr API key
     *
     * @var string
     */
    public $apiKey;

    /**
     * \Reference to REST client object
     *
     * @var  Client 
     */
    protected $_restClient = null;


    /**
     * Performs object initializations
     *
     *  # Sets up character encoding
     *  # Saves the API key
     *
     * @param  string $apiKey Your Flickr API key
     * @return void
     */
    public function __construct($apiKey)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->apiKey = (string) $apiKey;
    }


    /**
     * Find Flickr photos by tag.
     *
     * Query options include:
     *
     *  # per_page:        how many results to return per query
     *  # page:            the starting page offset.  first result will be (page - 1) * per_page + 1
     *  # tag_mode:        Either 'any' \for an OR combination \of tags,
     *                     or 'all' \for an AND combination. Default is 'any'.
     *  # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *  # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *  # min_taken_date:  Minimum upload date to search on.  Date should be a MySQL datetime.
     *  # max_taken_date:  Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param  string|array $query   A single tag or an array \of tags.
     * @param  array        $options Additional parameters to refine your query.
     * @return  ResultSet 
     * @throws  ServiceException 
     */
    public function tagSearch($query, array $options = array())
    {
        static $method = 'flickr.photos.search';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'tag_mode' => 'or',
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');

        $options['tags'] = is_array($query) ? implode(',', $query) : $query;

        $options = $this->\\prepareOptions($method, $options, $defaultOptions);

        $this->_validateTagSearch($options);

        // now search \for photos
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        if ($response->isError()) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        /**
         * @see  ResultSet 
         */
        require_once 'Zend/Service/Flickr/ResultSet.php';
        return new  ResultSet ($dom, $this);
    }


    /**
     * Finds photos by a user's username or email.
     *
     * Additional query options include:
     *
     *  # per_page:        how many results to return per query
     *  # page:            the starting page offset.  first result will be (page - 1) * per_page + 1
     *  # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *  # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *  # min_taken_date:  Minimum upload date to search on.  Date should be a MySQL datetime.
     *  # max_taken_date:  Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param  string $query   username or email
     * @param  array  $options Additional parameters to refine your query.
     * @return  ResultSet 
     * @throws  ServiceException 
     */
    public function userSearch($query, array $options = null)
    {
        static $method = 'flickr.people.getPublicPhotos';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');


        // can't access by username, must get ID first
        if (strchr($query, '@')) {
            // optimistically hope this is an email
            $options['user_id'] = $this->getIdByEmail($query);
        } else {
            // we can safely ignore this exception here
            $options['user_id'] = $this->getIdByUsername($query);
        }

        $options = $this->\\prepareOptions($method, $options, $defaultOptions);
        $this->_validateUserSearch($options);

        // now search \for photos
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        if ($response->isError()) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        /**
         * @see  ResultSet 
         */
        require_once 'Zend/Service/Flickr/ResultSet.php';
        return new  ResultSet ($dom, $this);
    }
    
    /**
     * Finds photos in a group's pool.
     *
     * @param  string $query   group id
     * @param  array  $options Additional parameters to refine your query.
     * @return  ResultSet 
     * @throws  ServiceException 
     */
    public function groupPoolGetPhotos($query, array $options = array())
    {
        static $method = 'flickr.groups.pools.getPhotos';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');

        if (empty($query) || !is_string($query)) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('You must supply a group id');
        }

        $options['group_id'] = $query;

        $options = $this->\\prepareOptions($method, $options, $defaultOptions);

        $this->_validateGroupPoolGetPhotos($options);

        // now search \for photos
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        if ($response->isError()) {
            /**
            * @see  ServiceException 
            */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        /**
        * @see  ResultSet 
        */
        require_once 'Zend/Service/Flickr/ResultSet.php';
        return new  ResultSet ($dom, $this);
    }



    /**
     * Utility function to find Flickr \User IDs \for usernames.
     *
     * (You can only find a user's photo with their NSID.)
     *
     * @param  string $username the username
     * @return string the NSID (userid)
     * @throws  ServiceException 
     */
    public function getIdByUsername($username)
    {
        static $method = 'flickr.people.findByUsername';

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'username' => (string) $username);

        if (empty($username)) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('You must supply a username');
        }

        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        if ($response->isError()) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        $xpath = new DOMXPath($dom);
        return (string) $xpath->query('//user')->item(0)->getAttribute('id');
    }


    /**
     * Utility function to find Flickr \User IDs \for emails.
     *
     * (You can only find a user's photo with their NSID.)
     *
     * @param  string $email the email
     * @return string the NSID (userid)
     * @throws  ServiceException 
     */
    public function getIdByEmail($email)
    {
        static $method = 'flickr.people.findByEmail';

        if (empty($email)) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('You must supply an e-mail address');
        }

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'find_email' => (string) $email);

        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        if ($response->isError()) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        $xpath = new DOMXPath($dom);
        return (string) $xpath->query('//user')->item(0)->getAttribute('id');
    }


    /**
     * Returns Flickr photo details by \for the given photo ID
     *
     * @param  string $id the NSID
     * @return array \of  Image , details \for the specified image
     * @throws  ServiceException 
     */
    public function getImageDetails($id)
    {
        static $method = 'flickr.photos.getSizes';

        if (empty($id)) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('You must supply a photo ID');
        }

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'photo_id' => $id);

        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        $xpath = new DOMXPath($dom);
        self::_checkErrors($dom);
        $retval = array();
        /**
         * @see  Image 
         */
        require_once 'Zend/Service/Flickr/Image.php';
        foreach ($xpath->query('//size') as $size) {
            $label = (string) $size->getAttribute('label');
            $retval[$label] = new  Image ($size);
        }

        return $retval;
    }


    /**
     * Returns a reference to the REST client, instantiating it if necessary
     *
     * @return  Client 
     */
    public function getRestClient()
    {
        if (null === $this->_restClient) {
            /**
             * @see  Client 
             */
            require_once 'Zend/Rest/Client.php';
            $this->_restClient = new  Client (self::URI_BASE);
        }

        return $this->_restClient;
    }


    /**
     * Validate \User Search Options
     *
     * @param  array $options
     * @return void
     * @throws  ServiceException 
     */
    protected function _validateUserSearch(array $options)
    {
        $validOptions = array('api_key', 'method', 'user_id', 'per_page', 'page', 'extras', 'min_upload_date',
                              'min_taken_date', 'max_upload_date', 'max_taken_date');

        $this->_compareOptions($options, $validOptions);

        /**
         * @see  Between 
         */
        require_once 'Zend/Validate/Between.php';
        $between = new  Between (1, 500, true);
        if (!$between->isValid($options['per_page'])) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ($options['per_page'] . ' is not valid \for the "per_page" option');
        }

        /**
         * @see  Int 
         */
        require_once 'Zend/Validate/Int.php';
        $int = new  Int ();
        if (!$int->isValid($options['page'])) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ($options['page'] . ' is not valid \for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if ($options['extras']) {
            $extras = explode(',', $options['extras']);
            $validExtras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $validExtras);
            }
        }
    }


    /**
     * Validate Tag Search Options
     *
     * @param  array $options
     * @return void
     * @throws  ServiceException 
     */
    protected function _validateTagSearch(array $options)
    {
        $validOptions = array('method', 'api_key', 'user_id', 'tags', 'tag_mode', 'text', 'min_upload_date',
                              'max_upload_date', 'min_taken_date', 'max_taken_date', 'license', 'sort',
                              'privacy_filter', 'bbox', 'accuracy', 'machine_tags', 'machine_tag_mode', 'group_id',
                              'extras', 'per_page', 'page');

        $this->_compareOptions($options, $validOptions);

        /**
         * @see  Between 
         */
        require_once 'Zend/Validate/Between.php';
        $between = new  Between (1, 500, true);
        if (!$between->isValid($options['per_page'])) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ($options['per_page'] . ' is not valid \for the "per_page" option');
        }

        /**
         * @see  Int 
         */
        require_once 'Zend/Validate/Int.php';
        $int = new  Int ();
        if (!$int->isValid($options['page'])) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ($options['page'] . ' is not valid \for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if ($options['extras']) {
            $extras = explode(',', $options['extras']);
            $validExtras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $validExtras);
            }
        }

    }
    
    
    /**
    * Validate Group Search Options
    *
    * @param  array $options
    * @throws  ServiceException 
    * @return void
    */
    protected function _validateGroupPoolGetPhotos(array $options)
    {
        $validOptions = array('api_key', 'tags', 'method', 'group_id', 'per_page', 'page', 'extras', 'user_id');

        $this->_compareOptions($options, $validOptions);

        /**
        * @see  Between 
        */
        require_once 'Zend/Validate/Between.php';
        $between = new  Between (1, 500, true);
        if (!$between->isValid($options['per_page'])) {
            /**
            * @see  ServiceException 
            */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ($options['per_page'] . ' is not valid \for the "per_page" option');
        }

        /**
        * @see  Int 
        */
        require_once 'Zend/Validate/Int.php';
        $int = new  Int ();
        
        if (!$int->isValid($options['page'])) {
            /**
            * @see  ServiceException 
            */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ($options['page'] . ' is not valid \for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if (isset($options['extras'])) {
            $extras = explode(',', $options['extras']);
            $validExtras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach($extras as $extra) {
                /**
                * @todo The following does not do anything [yet], so it is commented out.
                */
                //in_array(trim($extra), $validExtras);
            }
        }
    }


    /**
     * Throws an exception if and only if the response status indicates a failure
     *
     * @param  DOMDocument $dom
     * @return void
     * @throws  ServiceException 
     */
    protected static function _checkErrors(DOMDocument $dom)
    {
        if ($dom->documentElement->getAttribute('stat') === 'fail') {
            $xpath = new DOMXPath($dom);
            $err = $xpath->query('//err')->item(0);
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('Search failed due to error: ' . $err->getAttribute('msg')
                                           . ' (error #' . $err->getAttribute('code') . ')');
        }
    }


    /**
     * Prepare options \for the request
     *
     * @param  string $method         Flickr Method to call
     * @param  array  $options        \User Options
     * @param  array  $defaultOptions Default Options
     * @return array Merged array \of user and default/required options
     */
    protected function \\prepareOptions($method, array $options, array $defaultOptions)
    {
        $options['method']  = (string) $method;
        $options['api_key'] = $this->apiKey;

        return array_merge($defaultOptions, $options);
    }


    /**
     * Throws an exception if and only if any user options are invalid
     *
     * @param  array $options      \User options
     * @param  array $validOptions Valid options
     * @return void
     * @throws  ServiceException 
     */
    protected function _compareOptions(array $options, array $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);
        if ($difference) {
            /**
             * @see  ServiceException 
             */
            require_once 'Zend/Service/Exception.php';
            throw new  ServiceException ('The following parameters are invalid: ' . implode(',', $difference));
        }
    }
}

