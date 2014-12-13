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
 * @version    $Id: Regex.php 8064 2008-02-16 10:58:39Z thomas $
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


use Zend\Validate\Exception as ValidateException;
use Zend\Validate\ValidateAbstract as ValidateAbstract;




class  Regex  extends  ValidateAbstract 
{

    const NOT_MATCH = 'regexNotMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => "'%value%' does not match against pattern '%pattern%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'pattern' => '_pattern'
    );

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern;

    /**
     * Sets validator options
     *
     * @param  string $pattern
     * @return void
     */
    public function __construct($pattern)
    {
        $this->setPattern($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * Sets the pattern option
     *
     * @param  string $pattern
     * @return  Regex  Provides a fluent interface
     */
    public function setPattern($pattern)
    {
        $this->_pattern = (string) $pattern;
        return $this;
    }

    /**
     * Defined by \Zend\Validate\ValidateInterface
     *
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  string $value
     * @throws  ValidateException  if there is a fatal error in pattern matching
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        $status = @preg_match($this->_pattern, $valueString);
        if (false === $status) {
            /**
             * @see  ValidateException 
             */
            require_once 'Zend/Validate/Exception.php';
            throw new  ValidateException ("Internal error matching pattern '$this->_pattern' against value '$valueString'");
        }
        if (!$status) {
            $this->_error();
            return false;
        }
        return true;
    }

}
