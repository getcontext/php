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
 * @version    $Id: Barcode.php 8211 2008-02-20 14:29:24Z darby $
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




class  Barcode  extends  ValidateAbstract 
{
    /**
     * Barcode validator
     *
     * @var  ValidateAbstract 
     */
    protected $_barcodeValidator;

    /**
     * Generates the standard validator object
     *
     * @param  string $barcodeType - Barcode validator to use
     * @return void
     * @throws  ValidateException 
     */
    public function __construct($barcodeType)
    {
        $this->setType($barcodeType);
    }

    /**
     * Sets a new barcode validator
     *
     * @param  string $barcodeType - Barcode validator to use
     * @return void
     * @throws  ValidateException 
     */
    public function setType($barcodeType)
    {
        switch (strtolower($barcodeType)) {
            case 'upc':
            case 'upc-a':
                $className = 'UpcA';
                break;
            case 'ean13':
            case 'ean-13':
                $className = 'Ean13';
                break;
            default:
                require_once 'Zend/Validate/Exception.php';
                throw new  ValidateException ("Barcode type '$barcodeType' is not supported'");
                break;
        }

        require_once 'Zend/Validate/Barcode/' . $className . '.php';

        $class = '\Zend\Validate\Barcode_' . $className;
        $this->_barcodeValidator = new $class;
    }

    /**
     * Defined by \Zend\Validate\ValidateInterface
     *
     * Returns true if and only if $value contains a valid barcode
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        return call_user_func(array($this->_barcodeValidator, 'isValid'), $value);
    }
}
