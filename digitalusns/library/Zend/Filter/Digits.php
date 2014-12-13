<?php

namespace Zend\Filter;



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
 * @package    \Zend\Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Digits.php 8731 2008-03-10 15:08:03Z darby $
 */


/**
 * @see  FilterInterface 
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Zend
 * @package    \Zend\Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Filter\FilterInterface as FilterInterface;




class  Digits  implements  FilterInterface 
{
    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var mixed
     **/
    protected static $_unicodeEnabled;

    /**
     * Class constructor
     *
     * Checks if PCRE is compiled with UTF-8 and Unicode support
     *
     * @return void
     */
    public function __construct()
    {
        if (null === self::$_unicodeEnabled) {
            self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }
    }

    /**
     * Defined by  FilterInterface 
     *
     * Returns the string $value, removing all but digit characters
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative 0-9 match
            $pattern = '/[^0-9]/';
        } else if (extension_loaded('mbstring')) {
            // Filter \for the value with mbstring
            $pattern = '/[^[:digit:]]/';
        } else {
            // Filter \for the value without mbstring
            $pattern = '/[\p{^N}]/';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
