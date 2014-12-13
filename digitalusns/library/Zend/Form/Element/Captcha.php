<?php

namespace Zend\Form\Element;



/**  Xhtml  */
require_once 'Zend/Form/Element/Xhtml.php';

/**  Adapter  */
require_once 'Zend/Captcha/Adapter.php';

/**
 * Generic captcha element
 * 
 * This element allows to insert CAPTCHA into the form in order
 * to validate that human is submitting the form. The actual
 * logic is contained in the captcha adapter.
 * 
 * @see http://en.wikipedia.org/wiki/Captcha
 *
 */


use Zend\Loader\PluginLoader as PluginLoader;
use Zend\Form\Element\Xhtml as Xhtml;
use Zend\Captcha\Adapter as Adapter;
use Zend\View\ViewInterface as ViewInterface;
use Zend\Form\Element as Element;
use Zend\Config as Config;




class  Captcha  extends  Xhtml  
{
    /**
     * @const string Captch plugin type constant
     */
    const CAPTCHA = 'CAPTCHA';

    /**
     * Captcha adapter
     *
     * @var  Adapter 
     */
    protected $_captcha;
    
	/**
	 * Get captcha adapter
	 * 
	 * @return  Adapter 
	 */
    public function getCaptcha() 
    {
		return $this->_captcha;
	}
	
	/**
	 * Set captcha adapter
	 * 
	 * @param string|array| Adapter  $captcha
	 * @param array $options
	 */
    public function setCaptcha($captcha, $options = array()) 
    {
	    if ($captcha instanceof  Adapter ) {
            $instance = $captcha;
	    } else {
    	    if (is_array($captcha)) {
                if (array_key_exists('captcha', $captcha)) {
                    $name = $captcha['captcha'];
                    unset($captcha['captcha']);
                } else {
                    $name = array_shift($captcha);
                }
    	        $options = array_merge($options, $captcha);
    	    } else {
    	        $name = $captcha;
    	    }

    	    $name = $this->getPluginLoader(self::CAPTCHA)->load($name);
    	    if (empty($options)) {
                $instance = new $name;
            } else {
                $r = new \ReflectionClass($name);
                if ($r->hasMethod('__construct')) {
                    $instance = $r->newInstanceArgs(array($options));
                } else {
                    $instance = $r->newInstance();
                }
            }
	    }
	    
        $this->_captcha = $instance;
        $this->_captcha->setName($this->getName());
        return $this;
	}

    /**
     * Constructor
     *
     * $spec may be:
     * - string: name \of element
     * - array: options with which to configure element
     * -  Config :  Config  with options \for configuring element
     * 
     * @param  string|array| Config  $spec 
     * @return void
     */
	public function __construct($spec, $options = null) 
	{
		parent::__construct($spec, $options);
    	$this->setAllowEmpty(true)
    	     ->setRequired(true)
             ->setAutoInsertNotEmptyValidator(false)
    	     ->addValidator($this->getCaptcha(), true);
    }	

    /**
     * Set options
     *
     * Overrides to allow passing captcha options
     * 
     * @param  array $options 
     * @return  Captcha 
     */
    public function setOptions(array $options)
    {
        if (array_key_exists('captcha', $options)) {
            if (array_key_exists('captchaOptions', $options)) {
                $this->setCaptcha($options['captcha'], $options['captchaOptions']);
                unset($options['captchaOptions']);
            } else {
                $this->setCaptcha($options['captcha']);
            }
            unset($options['captcha']);
        }
        return parent::setOptions($options);
    }
    
    /**
     * Render form element
     * 
     * @param   ViewInterface  $view 
     * @return string
     */
    public function render( ViewInterface  $view = null)
    {
        $captcha    = $this->getCaptcha();
        $captcha->setName($this->getFullyQualifiedName());

        $decorators = $this->getDecorators();

        $decorator  = $captcha->getDecorator();
        if (!empty($decorator)) {
            array_unshift($decorators, $decorator);
        }

        $decorator = array('Captcha', array('captcha' => $captcha));
        array_unshift($decorators, $decorator);

        $this->setDecorators($decorators);

        $this->setValue($this->getCaptcha()->generate());

    	return parent::render($view);
    }
    
    /**
     * Retrieve plugin loader \for validator or filter chain
     *
     * Support \for plugin loader \for Captcha adapters 
     * 
     * @param  string $type 
     * @return  PluginLoader 
     * @throws \Zend\Loader\Exception on invalid type.
     */
    public function getPluginLoader($type)
    {
        $type = strtoupper($type);
        if ($type == self::CAPTCHA) {
            if (!isset($this->_loaders[$type])) {
                require_once 'Zend/Loader/PluginLoader.php';
                $this->_loaders[$type] = new  PluginLoader (
                    array('Zend_Captcha' => 'Zend/Captcha/')
                );
            }
            return $this->_loaders[$type];
        } else {
            return parent::getPluginLoader($type);
        }
    }
    
    /**
     * Add prefix path \for plugin loader \for captcha adapters
     *
     * This method handles the captcha type, the rest is handled by
     * the parent 
     *  
     * @param  string $path 
     * @return  Element 
     * @see  Element ::addPrefixPath
     */
    public function addPrefixPath($prefix, $path, $type = null)
    {
        $type = strtoupper($type);
        switch ($type) {
            case null:
                $loader = $this->getPluginLoader(self::CAPTCHA);
                $cPrefix = rtrim($prefix, '_') . '_Captcha';
                $cPath   = rtrim($path, '/\\') . '/Captcha';
                $loader->addPrefixPath($cPrefix, $cPath);
                return parent::addPrefixPath($prefix, $path, $type);
            case self::CAPTCHA:
                $loader = $this->getPluginLoader($type);
                $loader->addPrefixPath($prefix, $path);
                return $this;
            default:
                return parent::addPrefixPath($prefix, $path, $type = null);
        }
    }
    
    /**
     * Load default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Errors')
                 ->addDecorator('HtmlTag', array('tag' => 'dd'))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
    }

    /**
     * Is the captcha valid?
     * 
     * @param  mixed $value 
     * @param  mixed $context 
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->getCaptcha()->setName($this->getName());
        $belongsTo = $this->getBelongsTo();
        if (empty($belongsTo) || !is_array($context)) {
            return parent::isValid($value, $context);
        }

        $name     = $this->getFullyQualifiedName();
        $root     = substr($name, 0, strpos($name, '['));
        $segments = substr($name, strpos($name, '['));
        $segments = ltrim($segments, '[');
        $segments = rtrim($segments, ']');
        $segments = explode('][', $segments);
        array_unshift($segments, $root);
        array_pop($segments);
        $newContext = $context;
        foreach ($segments as $segment) {
            if (array_key_exists($segment, $newContext)) {
                $newContext = $newContext[$segment];
            }
        }

        return parent::isValid($value, $newContext);
    }
}
