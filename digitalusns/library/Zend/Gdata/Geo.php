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
 * Service class \for interacting with the services which use the 
 * GeoRSS + GML extensions.
 * @link http://georss.org/
 * @link http://www.opengis.net/gml/
 * @link http://code.google.com/apis/picasaweb/reference.html#georss_reference
 *
 * @category   Zend
 * @package     Gdata 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Http\Client as Client;
use Zend\Gdata as Gdata;




class  Geo  extends  Gdata 
{

    public static $namespaces = array(
            'georss' => 'http://www.georss.org/georss',
            'gml' => 'http://www.opengis.net/gml');

    /**
     * Create  Geo  object
     * 
     * @param  Client  $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $applicationId The identity \of the app in the form \of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('\Zend\Gdata\Geo');
        $this->registerPackage('\Zend\Gdata\Geo_Extension');
        parent::__construct($client, $applicationId);
    }

}
