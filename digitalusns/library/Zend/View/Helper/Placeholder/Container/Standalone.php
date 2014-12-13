<?php

namespace Zend\View\Helper\Placeholder\Container;


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
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Standalone.php 10665 2008-08-05 10:57:18Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Registry  */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/**  ViewHelperAbstract .php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Base class \for targetted placeholder helpers
 *
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 



use Zend\View\Helper\Placeholder\Container\ContainerAbstract as ViewHelperPlaceholderContainerAbstract;
use Zend\View\Helper\Placeholder\Registry as Registry;
use Zend\View\Helper\HelperAbstract as ViewHelperAbstract;
use Zend\View\ViewInterface as ViewInterface;
use Zend\View\Exception as ViewException;



abstract class  Standalone  extends  ViewHelperAbstract  implements \IteratorAggregate, \Countable, \ArrayAccess
{  
    /**
     * @var  ViewHelperPlaceholderContainerAbstract 
     */
    protected $_container;

    /**
     * @var  Registry 
     */
    protected $_registry;

    /**
     * Registry key under which container registers itself
     * @var string
     */
    protected $_regKey;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->setRegistry( Registry ::getRegistry());
        $registry = $this->getRegistry();
        $this->setContainer($this->getRegistry()->getContainer($this->_regKey));
    }

    /**
     * Retrieve registry
     * 
     * @return  Registry 
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Set registry object 
     * 
     * @param   Registry  $registry 
     * @return  Standalone 
     */
    public function setRegistry( Registry  $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Escape a string
     * 
     * @param  string $string 
     * @return string
     */
    protected function _escape($string)
    {
        if ($this->view instanceof  ViewInterface ) {
            return $this->view->escape($string);
        }

        return htmlentities((string) $string, null, 'UTF-8');
    }

    /**
     * Set container on which to operate
     * 
     * @param   ViewHelperPlaceholderContainerAbstract  $container 
     * @return  Standalone 
     */
    public function setContainer( ViewHelperPlaceholderContainerAbstract  $container)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Retrieve placeholder container
     * 
     * @return  ViewHelperPlaceholderContainerAbstract 
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * Overloading: set property value
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return void
     */
    public function __set($key, $value)
    {
        $container = $this->getContainer();
        $container[$key] = $value;
    }

    /**
     * Overloading: retrieve property
     * 
     * @param  string $key 
     * @return mixed
     */
    public function __get($key)
    {
        $container = $this->getContainer();
        if (isset($container[$key])) {
            return $container[$key];
        }

        return null;
    }

    /**
     * Overloading: check if property is set
     * 
     * @param  string $key 
     * @return bool
     */
    public function __isset($key)
    {
        $container = $this->getContainer();
        return isset($container[$key]);
    }

    /**
     * Overloading: unset property
     * 
     * @param  string $key 
     * @return void
     */
    public function __unset($key)
    {
        $container = $this->getContainer();
        if (isset($container[$key])) {
            unset($container[$key]);
        }
    }

    /**
     * Overload
     *
     * Proxy to container methods
     * 
     * @param  string $method 
     * @param  array $args 
     * @return mixed
     */
    public function __call($method, $args)
    {
        $container = $this->getContainer();
        if (method_exists($container, $method)) {
            $return = call_user_func_array(array($container, $method), $args);
            if ($return === $container) {
                // If the container is returned, we really want the current object
                return $this;
            }
            return $return;
        }

        require_once 'Zend/View/Exception.php';
        throw new  ViewException ('Method "' . $method . '" does not exist');
    }

    /**
     * String representation
     * 
     * @return string
     */
    public function toString()
    {
        return $this->getContainer()->toString();
    }

    /**
     * Cast to string representation
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * \Countable
     * 
     * @return int
     */
    public function count()
    {
        $container = $this->getContainer();
        return count($container);
    }

    /**
     * \ArrayAccess: offsetExists
     * 
     * @param  string|int $offset 
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->getContainer()->offsetExists($offset);
    }

    /**
     * \ArrayAccess: offsetGet
     * 
     * @param  string|int $offset 
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getContainer()->offsetGet($offset);
    }

    /**
     * \ArrayAccess: offsetSet
     * 
     * @param  string|int $offset 
     * @param  mixed $value 
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->getContainer()->offsetSet($offset, $value);
    }

    /**
     * \ArrayAccess: offsetUnset
     * 
     * @param  string|int $offset 
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->getContainer()->offsetUnset($offset);
    }

    /**
     * \IteratorAggregate: get \Iterator
     * 
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->getContainer()->getIterator();
    }
}
