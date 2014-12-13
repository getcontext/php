<?php

namespace Zend\Soap\Client;


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
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Client _Exception */
require_once 'Zend/Soap/Server/Exception.php';


/**  Server  */
require_once 'Zend/Soap/Server.php';

/**  Client  */
require_once 'Zend/Soap/Client.php';


if (extension_loaded('soap')) {

/**
 *  Local 
 * 
 * Class is intended to be used as local SOAP client which works
 * with a provided Server object.
 * 
 * Could be used \for development or testing purposes.
 * 
 * @category   Zend
 * @package    Zend_Soap
 */


use Zend\Soap\Client\Common as Common;
use Zend\Soap\Client as Client;
use Zend\Soap\Server as Server;




class  Local  extends  Client 
{
    /**
     * Server object
     *
     * @var  Server 
     */
    protected $_server;

    /**
     * Local client constructor
     *
     * @param  Server  $server
     * @param string $wsdl
     * @param array $options
     */
    function __construct( Server  $server, $wsdl, $options)
    {
    	$this->_server = $server;

        // Use Server specified SOAP version as default
        $this->setSoapVersion($server->getSoapVersion());

    	parent::__construct($wsdl, $options);
    }

    /**
     * Actual "do request" method.
     *
     * @internal
     * @param  Common  $client
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $one_way
     * @return mixed
     */
    public function _doRequest( Common  $client, $request, $location, $action, $version, $one_way = null)
    {
        // Perform request as is
        ob_start();
        $this->_server->handle($request);
        $response = ob_get_contents();
        ob_end_clean();
        
        return $response;
    }
}

} // end if (extension_loaded('soap')
