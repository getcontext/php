<?php

namespace Zend\Config;


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
 * @package     Config 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Ini.php 11181 2008-09-01 09:41:44Z alexander $
 */


/**
 * @see  Config 
 */
require_once 'Zend/Config.php';


/**
 * @category   Zend
 * @package     Config 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Config\Exception as ConfigException;
use Zend\Config as Config;




class  Ini  extends  Config 
{
    /**
     * String that separates nesting levels \of configuration data identifiers
     *
     * @var string
     */
    protected $_nestSeparator = '.';

    /**
     * Loads the section $section from the config file $filename \for
     * access facilitated by nested object properties.
     *
     * If the section name contains a ":" then the section name to the right
     * is loaded and included into the properties. \Note that the keys in
     * this $section will override any keys \of the same
     * name in the sections that have been included via ":".
     *
     * If the $section is null, then all sections in the ini file are loaded.
     *
     * If any key includes a ".", then this will act as a separator to
     * create a sub-property.
     *
     * example ini file:
     *      [all]
     *      db.connection = database
     *      hostname = live
     *
     *      [staging : all]
     *      hostname = staging
     *
     * after calling $data = new  Ini ($file, 'staging'); then
     *      $data->hostname === "staging"
     *      $data->db->connection === "database"
     *
     * The $options parameter may be provided as either a boolean or an array.
     * If provided as a boolean, this sets the $allowModifications option \of
     *  Config . If provided as an array, there are two configuration
     * directives that may be set. For example:
     *
     * $options = array(
     *     'allowModifications' => false,
     *     'nestSeparator'      => '->'
     *      );
     *
     * @param  string        $filename
     * @param  string|null   $section
     * @param  boolean|array $options
     * @throws  ConfigException 
     * @return void
     */
    public function __construct($filename, $section = null, $options = false)
    {
        if (empty($filename)) {
            /**
             * @see  ConfigException 
             */
            require_once 'Zend/Config/Exception.php';
            throw new  ConfigException ('Filename is not set');
        }

        $allowModifications = false;
        if (is_bool($options)) {
            $allowModifications = $options;
        } elseif (is_array($options)) {
            if (isset($options['allowModifications'])) {
                $allowModifications = (bool) $options['allowModifications'];
            }
            if (isset($options['nestSeparator'])) {
                $this->_nestSeparator = (string) $options['nestSeparator'];
            }
        }

        set_error_handler(array($this, '_loadFileErrorHandler'));
        $iniArray = parse_ini_file($filename, true); // Warnings and errors are suppressed
        restore_error_handler();
        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
            throw new  ConfigException ($this->_loadFileErrorStr);
        }
        
        $preProcessedArray = array();
        foreach ($iniArray as $key => $data)
        {
            $bits = explode(':', $key);
            $thisSection = trim($bits[0]);
            switch (count($bits)) {
                case 1:
                    $preProcessedArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($bits[1]);
                    $preProcessedArray[$thisSection] = array_merge(array(';extends'=>$extendedSection), $data);
                    break;

                default:
                    /**
                     * @see  ConfigException 
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new  ConfigException ("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }

        if (null === $section) {
            $dataArray = array();
            foreach ($preProcessedArray as $sectionName => $sectionData) {
                if(!is_array($sectionData)) {
                    $dataArray = array_merge_recursive($dataArray, $this->_processKey(array(), $sectionName, $sectionData));
                } else {
                    $dataArray[$sectionName] = $this->_processExtends($preProcessedArray, $sectionName);
                }
            }
            parent::__construct($dataArray, $allowModifications);
        } elseif (is_array($section)) {
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($preProcessedArray[$sectionName])) {
                    /**
                     * @see  ConfigException 
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new  ConfigException ("Section '$sectionName' cannot be found in $filename");
                }
                $dataArray = array_merge($this->_processExtends($preProcessedArray, $sectionName), $dataArray);

            }
            parent::__construct($dataArray, $allowModifications);
        } else {
            if (!isset($preProcessedArray[$section])) {
                /**
                 * @see  ConfigException 
                 */
                require_once 'Zend/Config/Exception.php';
                throw new  ConfigException ("Section '$section' cannot be found in $filename");
            }
            parent::__construct($this->_processExtends($preProcessedArray, $section), $allowModifications);
        }

        $this->_loadedSection = $section;
    }
    
    /**
     * Helper function to process each element in the section and handle
     * the "extends" inheritance keyword. Passes control to _processKey()
     * to handle the "dot" sub-property syntax in each key.
     *
     * @param  array  $iniArray
     * @param  string $section
     * @param  array  $config
     * @throws  ConfigException 
     * @return array
     */
    protected function _processExtends($iniArray, $section, $config = array())
    {
        $thisSection = $iniArray[$section];

        foreach ($thisSection as $key => $value) {
            if (strtolower($key) == ';extends') {
                if (isset($iniArray[$value])) {
                    $this->_assertValidExtend($section, $value);
                    $config = $this->_processExtends($iniArray, $value, $config);
                } else {
                    /**
                     * @see  ConfigException 
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new  ConfigException ("Section '$section' cannot be found");
                }
            } else {
                $config = $this->_processKey($config, $key, $value);
            }
        }
        return $config;
    }

    /**
     * Assign the key's value to the property list. Handle the "dot"
     * notation \for sub-properties by passing control to
     * processLevelsInKey().
     *
     * @param  array  $config
     * @param  string $key
     * @param  string $value
     * @throws  ConfigException 
     * @return array
     */
    protected function _processKey($config, $key, $value)
    {
        if (strpos($key, $this->_nestSeparator) !== false) {
            $pieces = explode($this->_nestSeparator, $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    $config[$pieces[0]] = array();
                } elseif (!is_array($config[$pieces[0]])) {
                    /**
                     * @see  ConfigException 
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new  ConfigException ("Cannot create sub-key \for '{$pieces[0]}' as key already exists");
                }
                $config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                /**
                 * @see  ConfigException 
                 */
                require_once 'Zend/Config/Exception.php';
                throw new  ConfigException ("Invalid key '$key'");
            }
        } else {
            $config[$key] = $value;
        }
        return $config;
    }
}
