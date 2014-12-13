<?php

namespace Zend\Dojo\View\Helper;


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
 * @package    \Zend\Dojo
 * @subpackage \View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Dijit.php 10079 2008-07-14 10:56:37Z matthew $
 */

/**  HtmlElement  */
require_once 'Zend/View/Helper/HtmlElement.php';

/**
 * Dojo dijit base class
 * 
 * @uses       \Zend\View\Helper\HelperAbstract
 * @package    \Zend\Dojo
 * @subpackage \View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */



use Zend\View\Helper\HtmlElement as HtmlElement;
use Zend\Dojo\View\Helper\Dojo as Dojo;
use Zend\View\ViewInterface as ViewInterface;
use Zend\Json as Json;



abstract class  Dijit  extends  HtmlElement 
{
    /**
     * @var  Dojo _Container
     */
    public $dojo;

    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit;

    /**
     * Element type
     * @var string
     */
    protected $_elementType;

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module;

    /**
     * Set view
     *
     * Set view and enable dojo
     * 
     * @param   ViewInterface  $view 
     * @return  Dijit 
     */
    public function setView( ViewInterface  $view)
    {
        parent::setView($view);
        $this->dojo = $this->view->dojo();
        $this->dojo->enable();
        return $this;
    }

    /**
     * Whether or not to use declarative dijit creation
     * 
     * @return bool
     */
    protected function _useDeclarative()
    {
        return  Dojo ::useDeclarative();
    }

    /**
     * Whether or not to use programmatic dijit creation
     * 
     * @return bool
     */
    protected function _useProgrammatic()
    {
        return  Dojo ::useProgrammatic();
    }

    /**
     * Whether or not to use programmatic dijit creation w/o script creation
     * 
     * @return bool
     */
    protected function _useProgrammaticNoScript()
    {
        return  Dojo ::useProgrammaticNoScript();
    }

    /**
     * Create a layout container
     * 
     * @param  int $id 
     * @param  string $content 
     * @param  array $params 
     * @param  array $attribs 
     * @param  string|null $dijit 
     * @return string
     */
    protected function _createLayoutContainer($id, $content, array $params, array $attribs, $dijit = null)
    {
        $attribs['id'] = $id;
        $attribs = $this->_prepareDijit($attribs, $params, 'layout', $dijit);
     
        $html = '<div' . $this->_htmlAttribs($attribs) . '>'
              . $content
              . "</div>\n";

        return $html;
    }

    /**
     * Create HTML representation \of a dijit form element
     * 
     * @param  string $id 
     * @param  string $value 
     * @param  array $params 
     * @param  array $attribs 
     * @param  string|null $dijit 
     * @return string
     */
    public function _createFormElement($id, $value, array $params, array $attribs, $dijit = null)
    {
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }
        $attribs['name']  = $id;
        $attribs['value'] = (string) $value;
        $attribs['type']  = $this->_elementType;

        $attribs = $this->_prepareDijit($attribs, $params, 'element', $dijit);

        $html = '<input' 
              . $this->_htmlAttribs($attribs) 
              . $this->getClosingBracket();
        return $html;
    }

    /**
     * Merge attributes and parameters
     *
     * Also sets up requires
     * 
     * @param  array $attribs 
     * @param  array $params 
     * @param  string $type 
     * @param  string $dijit Dijit type to use (otherwise, pull from $_dijit)
     * @return array
     */
    protected function _prepareDijit(array $attribs, array $params, $type, $dijit = null)
    {
        $this->dojo->requireModule($this->_module);

        switch ($type) {
            case 'layout':
                $stripParams = array('id');
                break;
            case 'element':
                $stripParams = array('id', 'name', 'value', 'type');
                foreach (array('checked', 'disabled', 'readonly') as $attrib) {
                    if (array_key_exists($attrib, $attribs)) {
                        if ($attribs[$attrib]) {
                            $attribs[$attrib] = $attrib;
                        } else {
                            unset($attribs[$attrib]);
                        }
                    }
                }
                break;
            case 'textarea':
                $stripParams = array('id', 'name', 'type');
                break;
            default:
        }

        foreach ($stripParams as $param) {
            if (array_key_exists($param, $params)) {
                unset($params[$param]);
            }
        }

        // Normalize constraints, if present
        if (array_key_exists('constraints', $params) && is_array($params['constraints'])) {
            require_once 'Zend/Json.php';
            $params['constraints'] =  Json ::encode($params['constraints']);
        }
        if (array_key_exists('constraints', $params) && $this->_useDeclarative()) {
            $params['constraints'] = str_replace('"', "'", $params['constraints']);
        }

        $dijit = (null === $dijit) ? $this->_dijit : $dijit;
        if ($this->_useDeclarative()) {
            $attribs = array_merge($attribs, $params);
            $attribs['dojoType'] = $dijit;
        } elseif (!$this->_useProgrammaticNoScript()) {
            $this->_createDijit($dijit, $attribs['id'], $params);
        }

        return $attribs;
    }

    /**
     * Create a dijit programmatically
     * 
     * @param  string $dijit 
     * @param  string $id 
     * @param  array $params 
     * @return void
     */
    protected function _createDijit($dijit, $id, array $params)
    {
        $params['dojoType'] = $dijit;

        array_walk_recursive($params, array($this, '_castBoolToString'));

        $this->dojo->setDijit($id, $params);
    }

    /**
     * Cast a boolean to a string value
     * 
     * @param  mixed $item 
     * @param  string $key 
     * @return void
     */
    protected function _castBoolToString(&$item, $key)
    {
        if (!is_bool($item)) {
            return;
        }
        $item = ($item) ? "true" : "false";
    }

    /**
     * Render a hidden element to hold a value
     * 
     * @param  string $id 
     * @param  string|int|float $value 
     * @return string
     */
    protected function _renderHiddenElement($id, $value)
    {
        $hiddenAttribs = array(
            'name'  => $id,
            'value' => (string) $value,
            'type'  => 'hidden',
        );
        return '<input' . $this->_htmlAttribs($hiddenAttribs) . $this->getClosingBracket();
    }
}
