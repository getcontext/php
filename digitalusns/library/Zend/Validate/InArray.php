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
 * @version    $Id: InArray.php 8064 2008-02-16 10:58:39Z thomas $
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




class  InArray  extends  ValidateAbstract 
{

    const NOT_IN_ARRAY = 'notInArray';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_IN_ARRAY => "'%value%' was not found in the haystack"
    );

    /**
     * Haystack \of possible values
     *
     * @var array
     */
    protected $_haystack;

    /**
     * Whether a strict in_array() invocation is used
     *
     * @var boolean
     */
    protected $_strict;

    /**
     * Sets validator options
     *
     * @param  array   $haystack
     * @param  boolean $strict
     * @return void
     */
    public function __construct(array $haystack, $strict = false)
    {
        $this->setHaystack($haystack)
             ->setStrict($strict);
    }

    /**
     * Returns the haystack option
     *
     * @return mixed
     */
    public function getHaystack()
    {
        return $this->_haystack;
    }

    /**
     * Sets the haystack option
     *
     * @param  mixed $haystack
     * @return  InArray  Provides a fluent interface
     */
    public function setHaystack(array $haystack)
    {
        $this->_haystack = $haystack;
        return $this;
    }

    /**
     * Returns the strict option
     *
     * @return boolean
     */
    public function getStrict()
    {
        return $this->_strict;
    }

    /**
     * Sets the strict option
     *
     * @param  boolean $strict
     * @return  InArray  Provides a fluent interface
     */
    public function setStrict($strict)
    {
        $this->_strict = $strict;
        return $this;
    }

    /**
     * Defined by \Zend\Validate\ValidateInterface
     *
     * Returns true if and only if $value is contained in the haystack option. If the strict
     * option is true, then the type \of $value is also checked.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        if (!in_array($value, $this->_haystack, $this->_strict)) {
            $this->_error();
            return false;
        }
        return true;
    }

}
