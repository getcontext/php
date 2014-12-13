<?php

namespace Zend\View\Helper;


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
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Translate.php 10665 2008-08-05 10:57:18Z matthew $
 */

/**  Locale  */
require_once 'Zend/Locale.php';

/**  ViewHelperAbstract .php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Translation view helper
 *
 * @category  Zend
 * @package   \Zend\View
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\Helper\HelperAbstract as ViewHelperAbstract;
use Zend\Translate\Adapter as Adapter;
use Zend\View\Exception as ViewException;
use Zend\Translate as ZendTranslate;
use Zend\Registry as Registry;
use Zend\Locale as Locale;




class  Translate  extends  ViewHelperAbstract 
{
    /**
     * Translation object
     *
     * @var  Adapter 
     */
    protected $_translator;

    /**
     * Constructor \for manually handling
     *
     * @param  ZendTranslate | Adapter  $translate Instance \of  ZendTranslate 
     */
    public function __construct($translate = null)
    {
        if (empty($translate) === false) {
            $this->setTranslator($translate);
        }
    }

    /**
     * Translate a message
     * You can give multiple params or an array \of params.
     * If you want to output another locale just set it as last single parameter
     * Example 1: translate('%1\$s + %2\$s', $value1, $value2, $locale);
     * Example 2: translate('%1\$s + %2\$s', array($value1, $value2), $locale);
     *
     * @param  string $messageid Id \of the message to be translated
     * @return string Translated message
     */
    public function translate($messageid = null)
    {
        if ($messageid === null) {
            return $this;
        }

        $translate = $this->getTranslator();
        if ($translate === null) {
            return $messageid;
        }

        $options = func_get_args();
        array_shift($options);

        $count  = count($options);
        $locale = null;
        if ($count > 0) {
            if ( Locale ::isLocale($options[($count - 1)]) !== false) {
                $locale = array_pop($options);
            }
        }

        if ((count($options) === 1) and (is_array($options[0]) === true)) {
            $options = $options[0];
        }

        $message = $translate->translate($messageid, $locale);
        if ($count === 0) {
            return $message;
        }

        return vsprintf($message, $options);
    }

    /**
     * Sets a translation Adapter \for translation
     *
     * @param   ZendTranslate | Adapter  $translate Instance \of  ZendTranslate 
     * @throws  ViewException  When no or a false instance was set
     * @return  Translate 
     */
    public function setTranslator($translate)
    {
        if ($translate instanceof  Adapter ) {
            $this->_translator = $translate;
        } else if ($translate instanceof  ZendTranslate ) {
            $this->_translator = $translate->getAdapter();
        } else {
            require_once 'Zend/View/Exception.php';
            throw new  ViewException ('You must set an instance \of  ZendTranslate  or  ZendTranslate _Adapter');
        }

        return $this;
    }

    /**
     * Retrieve translation object
     *
     * If none is currently registered, attempts to pull it from the registry
     * using the key '\Zend\Translate'.
     *
     * @return  Adapter |null
     */
    public function getTranslator()
    {
        if ($this->_translator === null) {
            require_once 'Zend/Registry.php';
            if ( Registry ::isRegistered('\Zend\Translate') === true) {
                $this->setTranslator( Registry ::get('\Zend\Translate'));
            }
        }

        return $this->_translator;
    }

    /**
     * Set's an new locale \for all further translations
     *
     * @param  string| Locale  $locale New locale to set
     * @throws  ViewException  When no  ZendTranslate  instance was set
     * @return  Translate 
     */
    public function setLocale($locale = null)
    {
        $translate = $this->getTranslator();
        if ($translate === null) {
            require_once 'Zend/View/Exception.php';
            throw new  ViewException ('You must set an instance \of  ZendTranslate  or  ZendTranslate _Adapter');
        }

        $translate->setLocale($locale);
        return $this;
    }

    /**
     * Returns the set locale \for translations
     *
     * @throws  ViewException  When no  ZendTranslate  instance was set
     * @return string| Locale 
     */
    public function getLocale()
    {
        $translate = $this->getTranslator();
        if ($translate === null) {
            require_once 'Zend/View/Exception.php';
            throw new  ViewException ('You must set an instance \of  ZendTranslate  or  ZendTranslate _Adapter');
        }

        return $translate->getLocale();
    }
}
