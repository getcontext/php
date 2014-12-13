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
 * @see \Zend\Gdata\Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * \Data model \for representing an atom:link element
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\Extension\HrefLang as HrefLang;
use Zend\Gdata\App\Extension\Length as Length;
use Zend\Gdata\App\Extension\Title as Title;
use Zend\Gdata\App\Extension\Href as Href;
use Zend\Gdata\App\Extension\Type as Type;
use Zend\Gdata\App\Extension\Rel as Rel;
use Zend\Gdata\App\Extension as Extension;




class  Link  extends  Extension 
{

    protected $_rootElement = 'link';
    protected $_href = null;
    protected $_rel = null;
    protected $_type = null;
    protected $_hrefLang = null;
    protected $_title = null;
    protected $_length = null;

    public function __construct($href = null, $rel = null, $type = null,
            $hrefLang = null, $title = null, $length = null)
    {
        parent::__construct();
        $this->_href = $href;
        $this->_rel = $rel;
        $this->_type = $type;
        $this->_hrefLang = $hrefLang;
        $this->_title = $title;
        $this->_length = $length;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_href != null) {
            $element->setAttribute('href', $this->_href);
        }
        if ($this->_rel != null) {
            $element->setAttribute('rel', $this->_rel);
        }
        if ($this->_type != null) {
            $element->setAttribute('type', $this->_type);
        }
        if ($this->_hrefLang != null) {
            $element->setAttribute('hreflang', $this->_hrefLang);
        }
        if ($this->_title != null) {
            $element->setAttribute('title', $this->_title);
        }
        if ($this->_length != null) {
            $element->setAttribute('length', $this->_length);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'href':
            $this->_href = $attribute->nodeValue;
            break;
        case 'rel':
            $this->_rel = $attribute->nodeValue;
            break;
        case 'type':
            $this->_type = $attribute->nodeValue;
            break;
        case 'hreflang':
            $this->_hrefLang = $attribute->nodeValue;
            break;
        case 'title':
            $this->_title = $attribute->nodeValue;
            break;
        case 'length':
            $this->_length = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return  Href 
     */
    public function getHref()
    {
        return $this->_href;
    }

    /**
     * @param  Href  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setHref($value)
    {
        $this->_href = $value;
        return $this;
    }

    /**
     * @return  Rel 
     */
    public function getRel()
    {
        return $this->_rel;
    }

    /**
     * @param  Rel  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setRel($value)
    {
        $this->_rel = $value;
        return $this;
    }

    /**
     * @return  Type 
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param  Type  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setType($value)
    {
        $this->_type = $value;
        return $this;
    }

    /**
     * @return  HrefLang 
     */
    public function getHrefLang()
    {
        return $this->_hrefLang;
    }

    /**
     * @param  HrefLang  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setHrefLang($value)
    {
        $this->_hrefLang = $value;
        return $this;
    }

    /**
     * @return  Title 
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param  Title  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setTitle($value)
    {
        $this->_title = $value;
        return $this;
    }

    /**
     * @return  Length 
     */
    public function getLength()
    {
        return $this->_length;
    }

    /**
     * @param  Length  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setLength($value)
    {
        $this->_length = $value;
        return $this;
    }

}
