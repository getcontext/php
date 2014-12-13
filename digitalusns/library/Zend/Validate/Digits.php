<?php

namespace Zend\Validate;



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
 * @package    \Zend\Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Digits.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * @see  ValidateAbstract 
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * @category   Zend
 * @package    \Zend\Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Validate\ValidateAbstract as ValidateAbstract;
use Zend\Filter\Digits as FilterDigits;




class  Digits  extends  ValidateAbstract 
{
    /**
     * Validation failure message key \for when the value contains non-digit characters
     */
    const NOT_DIGITS = 'notDigits';

    /**
     * Validation failure message key \for when the value is an empty string
     */
    const STRING_EMPTY = 'stringEmpty';

    /**
     * Digits filter used \for validation
     *
     * @var  FilterDigits 
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_DIGITS   => "'%value%' contains not only digit characters",
        self::STRING_EMPTY => "'%value%' is an empty string"
    );

    /**
     * Defined by \Zend\Validate\ValidateInterface
     *
     * Returns true if and only if $value only contains digit characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        if ('' === $valueString) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }

        if (null === self::$_filter) {
            /**
             * @see  FilterDigits 
             */
            require_once 'Zend/Filter/Digits.php';
            self::$_filter = new  FilterDigits ();
        }

        if ($valueString !== self::$_filter->filter($valueString)) {
            $this->_error(self::NOT_DIGITS);
            return false;
        }

        return true;
    }

}
