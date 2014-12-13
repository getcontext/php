<?php

namespace Zend\Gdata\App\Extension;



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
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  Extension 
 */
require_once 'Zend/Gdata/App/Extension.php';

/**
 * Represents the atom:generator element
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\Extension\Version as Version;
use Zend\Gdata\App\Extension\Uri as Uri;
use Zend\Gdata\App\Extension as Extension;




class  Generator  extends  Extension 
{

    protected $_rootElement = 'generator';
    protected $_uri = null;
    protected $_version = null;

    public function __construct($text = null, $uri = null, $version = null)
    {
        parent::__construct();
        $this->_text = $text;
        $this->_uri = $uri;
        $this->_version = $version;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_uri != null) {
            $element->setAttribute('uri', $this->_uri);
        }
        if ($this->_version != null) {
            $element->setAttribute('version', $this->_version);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'uri':
            $this->_uri = $attribute->nodeValue;
            break;
        case 'version':
            $this->_version= $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return  Uri 
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @param  Uri  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setUri($value)
    {
        $this->_uri = $value;
        return $this;
    }

    /**
     * @return  Version 
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @param  Version  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setVersion($value)
    {
        $this->_version = $value;
        return $this;
    }

}
