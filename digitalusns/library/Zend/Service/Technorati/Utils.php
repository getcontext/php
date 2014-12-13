<?php

namespace Zend\Service\Technorati;


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
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Utils.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * Collection \of utilities \for various \Zend\Service\Technorati classes.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Technorati\Exception as ServiceTechnoratiException;
use Zend\Uri\Http as Http;
use Zend\Date as Date;
use Zend\Uri as Uri;




class  Utils 
{
    /**
     * Parses, validates and returns a valid  Uri  object
     * from given $input.
     *
     * @param   string| Http  $input
     * @return  null| Http 
     * @throws   ServiceTechnoratiException 
     * @static
     */
    public static function normalizeUriHttp($input)
    {
        // allow null as value
        if ($input === null) {
            return null;
        }

        /**
         * @see  Uri 
         */
        require_once 'Zend/Uri.php';
        if ($input instanceof  Http ) {
            $uri = $input;
        } else {
            try {
                $uri =  Uri ::factory((string) $input);
            }
            // wrap exception under  ServiceTechnoratiException  object
            catch (\Exception $e) {
                /**
                 * @see  ServiceTechnoratiException 
                 */
                require_once 'Zend/Service/Technorati/Exception.php';
                throw new  ServiceTechnoratiException ($e->getMessage());
            }
        }

        // allow inly  Http  objects or child classes
        if (!($uri instanceof  Http )) {
            /**
             * @see  ServiceTechnoratiException 
             */
            require_once 'Zend/Service/Technorati/Exception.php'; 
            throw new  ServiceTechnoratiException (
                "Invalid URL $uri, only HTTP(S) protocols can be used");
        }
        
        return $uri;
    }
    /**
     * Parses, validates and returns a valid  Date  object
     * from given $input.
     * 
     * $input can be either a string, an integer or a  Date  object.
     * If $input is string or int, it will be provided to  Date  as it is.
     * If $input is a  Date  object, the object instance will be returned. 
     *
     * @param   mixed| Date  $input
     * @return  null| Date 
     * @throws   ServiceTechnoratiException 
     * @static
     */
    public static function normalizeDate($input)
    {
        /**
         * @see  Date 
         */
        require_once 'Zend/Date.php';
        /**
         * @see \Zend\Locale
         */
        require_once 'Zend/Locale.php';
        
        // allow null as value and return valid  Date  objects
        if (($input === null) || ($input instanceof  Date )) {
            return $input;
        }
        
        // due to a BC break as \of ZF 1.5 it's not safe to use  Date ::isDate() here
        // see ZF-2524, ZF-2334
        if (@strtotime($input) !== FALSE) {
            return new  Date ($input);
        } else {
            /**
             * @see  ServiceTechnoratiException 
             */
            require_once 'Zend/Service/Technorati/Exception.php';
            throw new  ServiceTechnoratiException ("'$input' is not a valid Date/Time");
        }
    }
    
    /**
     * @todo public static function xpathQueryAndSet() {}
     */

    /**
     * @todo public static function xpathQueryAndSetIf() {}
     */

    /**
     * @todo public static function xpathQueryAndSetUnless() {}
     */
}
