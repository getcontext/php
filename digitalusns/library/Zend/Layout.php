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
 * @package     Layout 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Layout.php 10746 2008-08-07 05:35:31Z ralph $
 */

/**
 * Provide Layout support \for MVC applications
 *
 * @category   Zend
 * @package     Layout 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\Helper\Placeholder\Registry as Registry;
use Zend\Controller\Action\HelperBroker as HelperBroker;
use Zend\Controller\Front as Front;
use Zend\Filter\Inflector as Inflector;
use Zend\Layout\Exception as LayoutException;
use Zend\View\ViewInterface as ViewInterface;
use Zend\Loader as Loader;
use Zend\Config as Config;




class  Layout 
{
    /**
     * Placeholder container \for layout variables
     * @var \Zend\View\Helper\Placeholder\Container
     */
    protected $_container;

    /**
     * Key used to store content from 'default' named response segment
     * @var string
     */
    protected $_contentKey = 'content';

    /**
     * Are layouts enabled?
     * @var bool
     */
    protected $_enabled = true;

    /**
     * Helper class
     * @var string
     */
    protected $_helperClass = '\Zend\Layout\Controller\Action\Helper\Layout';
 
    /**
     * Inflector used to resolve layout script
     * @var  Inflector 
     */
    protected $_inflector;

    /**
     * Flag: is inflector enabled?
     * @var bool
     */
    protected $_inflectorEnabled = true;

    /**
     * Inflector target
     * @var string
     */
    protected $_inflectorTarget = ':script.:suffix';

    /**
     * Layout view
     * @var string
     */
    protected $_layout = 'layout';

    /**
     * Layout view script path
     * @var string
     */
    protected $_viewScriptPath = null;
    
    protected $_viewBasePath = null;
    protected $_viewBasePrefix = 'Layout_View';

    /**
     * Flag: is MVC integration enabled?
     * @var bool
     */
    protected $_mvcEnabled = true;

    /**
     * Instance registered with MVC, if any
     * @var  Layout 
     */
    protected static $_mvcInstance;

    /**
     * Flag: is MVC successful action only flag set?
     * @var bool
     */
    protected $_mvcSuccessfulActionOnly = true;

    /**
     * Plugin class
     * @var string
     */
    protected $_pluginClass = '\Zend\Layout\Controller\Plugin\Layout';
    
    /**
     * @var  ViewInterface 
     */
    protected $_view;

    /**
     * \View script suffix \for layout script
     * @var string
     */
    protected $_viewSuffix = 'phtml';

    /**
     * Constructor
     *
     * Accepts either:
     * - A string path to layouts
     * - An array \of options
     * - A  Config  object with options
     *
     * Layout script path, either as argument or as key in options, is 
     * required.
     *
     * If mvcEnabled flag is false from options, simply sets layout script path. 
     * Otherwise, also instantiates and registers action helper and controller 
     * plugin.
     * 
     * @param  string|array| Config  $options 
     * @return void
     */ 
    public function __construct($options = null, $initMvc = false) 
    { 
        if (null !== $options) {
            if (is_string($options)) {
                $this->setLayoutPath($options);
            } elseif (is_array($options)) {
                $this->setOptions($options);
            } elseif ($options instanceof  Config ) {
                $this->setConfig($options);
            } else {
                require_once 'Zend/Layout/Exception.php';
                throw new  LayoutException ('Invalid option provided to constructor');
            }
        }

        $this->_initVarContainer();

        if ($initMvc) {
            $this->_setMvcEnabled(true);
            $this->_initMvc();
        } else {
            $this->_setMvcEnabled(false);
        }
    }

    /**
     * Static method \for initialization with MVC support
     * 
     * @param  string|array| Config  $options 
     * @return  Layout 
     */
    public static function startMvc($options = null)
    {
        if (null === self::$_mvcInstance) {
            self::$_mvcInstance = new self($options, true);
        } else {
            self::$_mvcInstance->setOptions($options);
        }

        return self::$_mvcInstance;
    }

    /**
     * Retrieve MVC instance \of  Layout  object
     * 
     * @return  Layout |null
     */
    public static function getMvcInstance()
    {
        return self::$_mvcInstance;
    }

    /**
     * Reset MVC instance
     *
     * Unregisters plugins and helpers, and destroys MVC layout instance.
     * 
     * @return void
     */
    public static function resetMvcInstance()
    {
        if (null !== self::$_mvcInstance) {
            $layout = self::$_mvcInstance;
            $pluginClass = $layout->getPluginClass();
            $front =  Front ::getInstance();
            if ($front->hasPlugin($pluginClass)) {
                $front->unregisterPlugin($pluginClass);
            }

            if ( HelperBroker ::hasHelper('layout')) {
                 HelperBroker ::removeHelper('layout');
            }

            unset($layout);
            self::$_mvcInstance = null;
        }
    }

    /**
     * Set options en masse
     * 
     * @param  array $options 
     * @return void
     */
    public function setOptions($options)
    {
        if ($options instanceof  Config ) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            require_once 'Zend/Layout/Exception.php';
            throw new  LayoutException ('setOptions() expects either an array or a  Config  object');
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Initialize MVC integration
     * 
     * @return void
     */
    protected function _initMvc()
    {
        $this->_initPlugin();
        $this->_initHelper();
    }

    /**
     * Initialize front controller plugin
     * 
     * @return void
     */
    protected function _initPlugin()
    {
        $pluginClass = $this->getPluginClass();
        require_once 'Zend/Controller/Front.php';
        $front =  Front ::getInstance();
        if (!$front->hasPlugin($pluginClass)) {
            require_once 'Zend/Loader.php';
             Loader ::loadClass($pluginClass);
            $front->registerPlugin(
                // register to run last | BUT before the ErrorHandler (if its available)
                new $pluginClass($this), 
                99
            );
        }
    }

    /**
     * Initialize action helper
     * 
     * @return void
     */
    protected function _initHelper()
    {
        $helperClass = $this->getHelperClass();
        require_once 'Zend/Controller/Action/HelperBroker.php';
        if (! HelperBroker ::hasHelper('layout')) {
            require_once 'Zend/Loader.php';
             Loader ::loadClass($helperClass);
             HelperBroker ::getStack()->offsetSet(-90, new $helperClass($this));
        }
    }

    /**
     * Set options from a config object
     * 
     * @param   Config  $config 
     * @return  Layout 
     */
    public function setConfig( Config  $config)
    {
        $this->setOptions($config->toArray());
        return $this;
    }

    /**
     * Initialize placeholder container \for layout vars
     * 
     * @return \Zend\View\Helper\Placeholder\Container
     */
    protected function _initVarContainer()
    {
        if (null === $this->_container) {
            require_once 'Zend/View/Helper/Placeholder/Registry.php';
            $this->_container =  Registry ::getRegistry()->getContainer(__CLASS__);
        }

        return $this->_container;
    }

    /**
     * Set layout script to use
     *
     * \Note: enables layout.
     * 
     * @param  string $name 
     * @return  Layout 
     */ 
    public function setLayout($name) 
    {
        $this->_layout = (string) $name;
        $this->enableLayout();
        return $this;
    }
 
    /**
     * Get current layout script
     * 
     * @return string
     */ 
    public function getLayout() 
    {
        return $this->_layout;
    } 
 
    /**
     * Disable layout
     *
     * @return  Layout 
     */ 
    public function disableLayout() 
    {
        $this->_enabled = false;
        return $this;
    } 

    /**
     * Enable layout 
     * 
     * @return  Layout 
     */
    public function enableLayout()
    {
        $this->_enabled = true;
        return $this;
    }

    /**
     * Is layout enabled?
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    
    public function setViewBasePath($path, $prefix = 'Layout_View')
    {
        $this->_viewBasePath = $path;
        $this->_viewBasePrefix = $prefix;
        return $this;
    }
    
    public function getViewBasePath()
    {
        return $this->_viewBasePath;
    }
    
    public function setViewScriptPath($path)
    {
        $this->_viewScriptPath = $path;
        return $this;
    }
    
    public function getViewScriptPath()
    {
        return $this->_viewScriptPath;
    }
    
    /**
     * Set layout script path
     * 
     * @param  string $path 
     * @return  Layout 
     */ 
    public function setLayoutPath($path) 
    {
        return $this->setViewScriptPath($path);
    } 
    
    /**
     * Get current layout script path
     * 
     * @return string
     */ 
    public function getLayoutPath() 
    {
        return $this->getViewScriptPath();
    } 

    /**
     * Set content key
     *
     * Key in namespace container denoting default content
     *
     * @param  string $contentKey
     * @return  Layout 
     */
    public function setContentKey($contentKey)
    {
        $this->_contentKey = (string) $contentKey;
        return $this;
    }

    /**
     * Retrieve content key
     *
     * @return string
     */
    public function getContentKey()
    {
        return $this->_contentKey;
    }

    /**
     * Set MVC enabled flag
     *
     * @param  bool $mvcEnabled
     * @return  Layout 
     */
    protected function _setMvcEnabled($mvcEnabled)
    {
        $this->_mvcEnabled = ($mvcEnabled) ? true : false;
        return $this;
    }

    /**
     * Retrieve MVC enabled flag
     *
     * @return bool
     */
    public function getMvcEnabled()
    {
        return $this->_mvcEnabled;
    }

    /**
     * Set MVC Successful Action Only flag
     *
     * @param bool $successfulActionOnly
     * @return  Layout 
     */
    public function setMvcSuccessfulActionOnly($successfulActionOnly)
    {
        $this->_mvcSuccessfulActionOnly = ($successfulActionOnly) ? true : false;
        return $this;
    }
    
    /**
     * Get MVC Successful Action Only Flag
     *
     * @return bool
     */
    public function getMvcSuccessfulActionOnly()
    {
        return $this->_mvcSuccessfulActionOnly;
    }
    
    /**
     * Set view object
     * 
     * @param   ViewInterface  $view
     * @return  Layout 
     */ 
    public function setView( ViewInterface  $view) 
    {
        $this->_view = $view;
        return $this;
    } 

    /**
     * Retrieve helper class
     *
     * @return string
     */
    public function getHelperClass()
    {
        return $this->_helperClass;
    }

    /**
     * Set helper class
     *
     * @param  string $helperClass
     * @return  Layout 
     */
    public function setHelperClass($helperClass)
    {
        $this->_helperClass = (string) $helperClass;
        return $this;
    }

    /**
     * Retrieve plugin class
     *
     * @return string
     */
    public function getPluginClass()
    {
        return $this->_pluginClass;
    }

    /**
     * Set plugin class
     *
     * @param  string $pluginClass
     * @return  Layout 
     */
    public function setPluginClass($pluginClass)
    {
        $this->_pluginClass = (string) $pluginClass;
        return $this;
    }
 
    /**
     * Get current view object
     *
     * If no view object currently set, retrieves it from the ViewRenderer.
     * 
     * @todo Set inflector from view renderer at same time
     * @return  ViewInterface 
     */ 
    public function getView() 
    {
        if (null === $this->_view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer =  HelperBroker ::getStaticHelper('viewRenderer');
            if (null === $viewRenderer->view) {
                $viewRenderer->initView();
            }
            $this->setView($viewRenderer->view);
        }
        return $this->_view;
    } 

    /**
     * Set layout view script suffix
     *
     * @param  string $viewSuffix
     * @return  Layout 
     */
    public function setViewSuffix($viewSuffix)
    {
        $this->_viewSuffix = (string) $viewSuffix;
        return $this;
    }
 
    /**
     * Retrieve layout view script suffix
     *
     * @return string
     */
    public function getViewSuffix()
    {
        return $this->_viewSuffix;
    }

    /**
     * Retrieve inflector target
     *
     * @return string
     */
    public function getInflectorTarget()
    {
        return $this->_inflectorTarget;
    }

    /**
     * Set inflector target
     *
     * @param  string $inflectorTarget
     * @return  Layout 
     */
    public function setInflectorTarget($inflectorTarget)
    {
        $this->_inflectorTarget = (string) $inflectorTarget;
        return $this;
    }

    /**
     * Set inflector to use when resolving layout names
     *
     * @param   Inflector  $inflector
     * @return  Layout 
     */
    public function setInflector( Inflector  $inflector)
    {
        $this->_inflector = $inflector;
        return $this;
    }

    /**
     * Retrieve inflector
     *
     * @return  Inflector 
     */
    public function getInflector()
    {
        if (null === $this->_inflector) {
            require_once 'Zend/Filter/Inflector.php';
            $inflector = new  Inflector ();
            $inflector->setTargetReference($this->_inflectorTarget)
                      ->addRules(array(':script' => array('Word_CamelCaseToDash', 'StringToLower')))
                      ->setStaticRuleReference('suffix', $this->_viewSuffix);
            $this->setInflector($inflector);
        }

        return $this->_inflector;
    }

    /**
     * Enable inflector
     * 
     * @return  Layout 
     */
    public function enableInflector()
    {
        $this->_inflectorEnabled = true;
        return $this;
    }

    /**
     * Disable inflector
     * 
     * @return  Layout 
     */
    public function disableInflector()
    {
        $this->_inflectorEnabled = false;
        return $this;
    }

    /**
     * Return status \of inflector enabled flag
     * 
     * @return bool
     */
    public function inflectorEnabled()
    {
        return $this->_inflectorEnabled;
    }

    /**
     * Set layout variable
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return void
     */ 
    public function __set($key, $value) 
    {
        $this->_container[$key] = $value;
    }
 
    /**
     * Get layout variable
     * 
     * @param  string $key
     * @return mixed
     */ 
    public function __get($key) 
    {
        if (isset($this->_container[$key])) {
            return $this->_container[$key];
        }

        return null;
    }
 
    /**
     * Is a layout variable set?
     *
     * @param  string $key
     * @return bool
     */ 
    public function __isset($key) 
    {
        return (isset($this->_container[$key]));
    } 
 
    /**
     * Unset a layout variable?
     *
     * @param  string $key
     * @return void
     */ 
    public function __unset($key) 
    {
        if (isset($this->_container[$key])) {
            unset($this->_container[$key]);
        }
    } 
 
    /**
     * Assign one or more layout variables
     * 
     * @param  mixed $spec Assoc array or string key; if assoc array, sets each
     * key as a layout variable
     * @param  mixed $value Value if $spec is a key
     * @return  Layout 
     * @throws  LayoutException  if non-array/string value passed to $spec
     */ 
    public function assign($spec, $value = null) 
    {
        if (is_array($spec)) {
            $orig = $this->_container->getArrayCopy();
            $merged = array_merge($orig, $spec);
            $this->_container->exchangeArray($merged);
            return $this;
        }

        if (is_string($spec)) {
            $this->_container[$spec] = $value;
            return $this;
        }

        require_once 'Zend/Layout/Exception.php';
        throw new  LayoutException ('Invalid values passed to assign()');
    }

    /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns 
     * layout variables to view, determines layout name using inflector, and 
     * renders layout view script.
     *
     * $name will be passed to the inflector as the key 'script'.
     * 
     * @param  mixed $name 
     * @return mixed
     */ 
    public function render($name = null) 
    { 
        if (null === $name) {
            $name = $this->getLayout();
        }

        if ($this->inflectorEnabled() && (null !== ($inflector = $this->getInflector())))
        {
            $name = $this->_inflector->filter(array('script' => $name));
        }

        $view = $this->getView();

        if (null !== ($path = $this->getViewScriptPath())) {
            $view->addScriptPath($path);
        } elseif (null !== ($path = $this->getViewBasePath())) {
            $view->addBasePath($path, $this->_viewBasePrefix);
        }

        return $view->render($name);
    }
}
