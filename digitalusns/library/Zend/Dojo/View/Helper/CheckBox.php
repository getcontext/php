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
 * @version    $Id: CheckBox.php 10991 2008-08-24 03:48:21Z matthew $
 */

/**  Dijit  */
require_once 'Zend/Dojo/View/Helper/Dijit.php';

/**
 * Dojo CheckBox dijit
 * 
 * @uses        Dijit 
 * @package    \Zend\Dojo
 * @subpackage \View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */


use Zend\View\Helper\FormCheckbox as FormCheckbox;
use Zend\Dojo\View\Helper\Dijit as Dijit;




class  CheckBox  extends  Dijit 
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.CheckBox';

    /**
     * Element type
     * @var string
     */
    protected $_elementType = 'checkbox';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.CheckBox';

    /**
     * dijit.form.CheckBox
     * 
     * @param  int $id 
     * @param  string $content 
     * @param  array $params  Parameters to use \for dijit creation
     * @param  array $attribs HTML attributes
     * @param  array $checkedOptions Should contain either two items, or the keys checkedValue and unCheckedValue
     * @return string
     */
    public function checkBox($id, $value = null, array $params = array(), array $attribs = array(), array $checkedOptions = null)
    {
        // Prepare the checkbox options
        require_once 'Zend/View/Helper/FormCheckbox.php';
        $checked = false;
        if (isset($attribs['checked']) && $attribs['checked']) {
            $checked = true;
        } elseif (isset($attribs['checked'])) {
            $checked = false;
        }
        $checkboxInfo =  FormCheckbox ::determineCheckboxInfo($value, $checked, $checkedOptions);
        $attribs['checked'] = $checkboxInfo['checked'];
        $attribs['id']      = $id;

        $attribs = $this->_prepareDijit($attribs, $params, 'element');

        // and now we create it:
        $html = '';
        if (!strstr($id, '[]')) {
            // hidden element \for unchecked value
            $html .= $this->_renderHiddenElement($id, $checkboxInfo['unCheckedValue']);
        }

        // and final element
        $html .= $this->_createFormElement($id, $checkboxInfo['checkedValue'], $params, $attribs);

        return $html;
    }
}
