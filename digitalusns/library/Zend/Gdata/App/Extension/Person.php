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
 * @see  Name 
 */
require_once 'Zend/Gdata/App/Extension/Name.php';

/**
 * @see  Email 
 */
require_once 'Zend/Gdata/App/Extension/Email.php';

/**
 * @see  Uri 
 */
require_once 'Zend/Gdata/App/Extension/Uri.php';

/**
 * Base class \for people (currently used by atom:author, atom:contributor)
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Gdata\App\Extension\Email as Email;
use Zend\Gdata\App\Extension\Name as Name;
use Zend\Gdata\App\Extension\Uri as Uri;
use Zend\Gdata\App\Extension as Extension;



abstract class  Person  extends  Extension 
{

    protected $_rootElement = null;
    protected $_name = null;
    protected $_email = null;
    protected $_uri = null;

    public function __construct($name = null, $email = null, $uri = null)
    {
        parent::__construct();
        $this->_name = $name;
        $this->_email = $email;
        $this->_uri = $uri;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_name != null) {
            $element->appendChild($this->_name->getDOM($element->ownerDocument));
        }
        if ($this->_email != null) {
            $element->appendChild($this->_email->getDOM($element->ownerDocument));
        }
        if ($this->_uri != null) {
            $element->appendChild($this->_uri->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'name':
            $name = new  Name ();
            $name->transferFromDOM($child);
            $this->_name = $name;
            break;
        case $this->lookupNamespace('atom') . ':' . 'email':
            $email = new  Email ();
            $email->transferFromDOM($child);
            $this->_email = $email;
            break;
        case $this->lookupNamespace('atom') . ':' . 'uri':
            $uri = new  Uri ();
            $uri->transferFromDOM($child);
            $this->_uri = $uri;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return  Name 
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param  Name  $value
     * @return \Zend\Gdata\App\Entry Provides a fluent interface
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }

    /**
     * @return  Email 
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param  Email  $value
     * @return  Person  Provides a fluent interface
     */
    public function setEmail($value)
    {
        $this->_email = $value;
        return $this;
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
     * @return  Person  Provides a fluent interface
     */
    public function setUri($value)
    {
        $this->_uri = $value;
        return $this;
    }

}
