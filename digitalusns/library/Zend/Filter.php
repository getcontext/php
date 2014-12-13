<?php

namespace Zend;



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
 * @package     Filter 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Filter.php 8434 2008-02-27 19:15:13Z darby $
 */


/**
 * @see  FilterInterface 
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Zend
 * @package     Filter 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Filter\FilterInterface as FilterInterface;
use Zend\Filter\Exception as FilterException;
use Zend\Loader as Loader;




class  Filter  implements  FilterInterface 
{
    /**
     * Filter chain
     *
     * @var array
     */
    protected $_filters = array();

    /**
     * Adds a filter to the end \of the chain
     *
     * @param   FilterInterface  $filter
     * @return  Filter  Provides a fluent interface
     */
    public function addFilter( FilterInterface  $filter)
    {
        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Returns $value filtered through each filter in the chain
     *
     * Filters are run in the order in which they were added to the chain (FIFO)
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $valueFiltered = $value;
        foreach ($this->_filters as $filter) {
            $valueFiltered = $filter->filter($valueFiltered);
        }
        return $valueFiltered;
    }

    /**
     * Returns a value filtered through a specified filter class, without requiring separate
     * instantiation \of the filter object.
     *
     * The first argument \of this method is a data input value, that you would have filtered.
     * The second argument is a string, which corresponds to the basename \of the filter class,
     * relative to the  Filter  namespace. This method automatically loads the class,
     * creates an instance, and applies the filter() method to the data input. You can also pass
     * an array \of constructor arguments, if they are needed \for the filter class.
     *
     * @param  mixed        $value
     * @param  string       $classBaseName
     * @param  array        $args          OPTIONAL
     * @param  array|string $namespaces    OPTIONAL
     * @return mixed
     * @throws  FilterException 
     */
    public static function get($value, $classBaseName, array $args = array(), $namespaces = array())
    {
        require_once 'Zend/Loader.php';
        $namespaces = array_merge(array('\Zend\Filter'), (array) $namespaces);
        foreach ($namespaces as $namespace) {
            $className = $namespace . '_' . ucfirst($classBaseName);
            try {
                @ Loader ::loadClass($className);
            } catch (\Zend_Exception $ze) {
                continue;
            }
            $class = new \ReflectionClass($className);
            if ($class->implementsInterface('\Zend\Filter\FilterInterface')) {
                if ($class->hasMethod('__construct')) {
                    $object = $class->newInstanceArgs($args);
                } else {
                    $object = $class->newInstance();
                }
                return $object->filter($value);
            }
        }
        require_once 'Zend/Filter/Exception.php';
        throw new  FilterException ("Filter class not found from basename '$classBaseName'");
    }
}
