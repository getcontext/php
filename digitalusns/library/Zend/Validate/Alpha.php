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
 * @version    $Id: Alpha.php 8064 2008-02-16 10:58:39Z thomas $
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
use Zend\Filter\Alpha as FilterAlpha;




class  Alpha  extends  ValidateAbstract 
{
    /**
     * Validation failure message key \for when the value contains non-alphabetic characters
     */
    const NOT_ALPHA = 'notAlpha';

    /**
     * Validation failure message key \for when the value is an empty string
     */
    const STRING_EMPTY = 'stringEmpty';

    /**
     * Whether to allow white space characters; off by default
     *
     * @var boolean
     */
    public $allowWhiteSpace;

    /**
     * Alphabetic filter used \for validation
     *
     * @var  FilterAlpha 
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_ALPHA    => "'%value%' has not only alphabetic characters",
        self::STRING_EMPTY => "'%value%' is an empty string"
    );

    /**
     * Sets default option values \for this instance
     *
     * @param  boolean $allowWhiteSpace
     * @return void
     */
    public function __construct($allowWhiteSpace = false)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
    }

    /**
     * Defined by \Zend\Validate\ValidateInterface
     *
     * Returns true if and only if $value contains only alphabetic characters
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
             * @see  FilterAlpha 
             */
            require_once 'Zend/Filter/Alpha.php';
            self::$_filter = new  FilterAlpha ();
        }

        self::$_filter->allowWhiteSpace = $this->allowWhiteSpace;

        if ($valueString !== self::$_filter->filter($valueString)) {
            $this->_error(self::NOT_ALPHA);
            return false;
        }

        return true;
    }

}
