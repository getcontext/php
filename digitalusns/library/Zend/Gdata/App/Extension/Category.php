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
 * Represents the atom:category element
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\Extension\Scheme as Scheme;
use Zend\Gdata\App\Extension\Label as Label;
use Zend\Gdata\App\Extension\Term as Term;
use Zend\Gdata\App\Extension as Extension;




class  Category  extends  Extension 
{

    protected $_rootElement = 'category';
    protected $_term = null;
    protected $_scheme = null;
    protected $_label = null;

    public function __construct($term = null, $scheme = null, $label=null)
    {
        parent::__construct();
        $this->_term = $term;
        $this->_scheme = $scheme;
        $this->_label = $label;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_term != null) {
            $element->setAttribute('term', $this->_term);
        }
        if ($this->_scheme != null) {
            $element->setAttribute('scheme', $this->_scheme);
        }
        if ($this->_label != null) {
            $element->setAttribute('label', $this->_label);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'term':
            $this->_term = $attribute->nodeValue;
            break;
        case 'scheme':
            $this->_scheme = $attribute->nodeValue;
            break;
        case 'label':
            $this->_label = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return  Term 
     */
    public function getTerm()
    {
        return $this->_term;
    }

    /**
     * @param  Term  $value
     * @return  Category  Provides a fluent interface
     */
    public function setTerm($value)
    {
        $this->_term = $value;
        return $this;
    }

    /**
     * @return  Scheme 
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

    /**
     * @param  Scheme  $value
     * @return  Category  Provides a fluent interface
     */
    public function setScheme($value)
    {
        $this->_scheme = $value;
        return $this;
    }

    /**

    /**
     * @return  Label 
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * @param  Label  $value
     * @return  Category  Provides a fluent interface
     */
    public function setLabel($value)
    {
        $this->_label = $value;
        return $this;
    }

}
