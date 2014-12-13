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
 * @version    $Id: Form.php 10196 2008-07-18 22:01:18Z matthew $
 */

/**  Dijit  */
require_once 'Zend/Dojo/View/Helper/Dijit.php';

/**
 * Dojo Form dijit
 * 
 * @uses        Dijit 
 * @package    \Zend\Dojo
 * @subpackage \View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */


use Zend\Dojo\View\Helper\Dijit as Dijit;
use Zend\View\Helper\Form as ViewHelperForm;




class  Form  extends  Dijit 
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.Form';

    /**
     * Module being used
     * @var string
     */
    protected $_module = 'dijit.form.Form';

    /**
     * @var  ViewHelperForm 
     */
    protected $_helper;

    /**
     * dijit.form.Form
     * 
     * @param  string $id 
     * @param  null|array $attribs HTML attributes
     * @param  false|string $content 
     * @return string
     */
    public function form($id, $attribs = null, $content = false)
    {
        if (!is_array($attribs)) {
            $attribs = (array) $attribs;
        }
        if (array_key_exists('id', $attribs)) {
            $attribs['name'] = $id;
        } else {
            $attribs['id'] = $id;
        }

        if (false === $content) {
            $content = '';
        }

        $attribs = $this->_prepareDijit($attribs, array(), 'layout');

        return $this->getFormHelper()->form($id, $attribs, $content);
    }

    /**
     * Get standard form helper
     * 
     * @return  ViewHelperForm 
     */
    public function getFormHelper()
    {
        if (null === $this->_helper) {
            require_once 'Zend/View/Helper/Form.php';
            $this->_helper = new  ViewHelperForm ;
            $this->_helper->setView($this->view);
        }
        return $this->_helper;
    }
}
