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
 * @version    $Id: DijitContainer.php 10067 2008-07-12 21:05:32Z matthew $
 */

/**  Dijit  */
require_once 'Zend/Dojo/View/Helper/Dijit.php';

/**
 * Dijit layout container base class
 * 
 * @uses        Dijit 
 * @package    \Zend\Dojo
 * @subpackage \View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */



use Zend\Dojo\View\Helper\Dijit as Dijit;
use Zend\Dojo\View\Exception as DojoViewException;



abstract class  DijitContainer  extends  Dijit 
{
    /**
     * Capture locks
     * @var array
     */
    protected $_captureLock = array();

    /**
     * Metadata information to use with captured content
     * @var array
     */
    protected $_captureInfo = array();

    /**
     * Begin capturing content \for layout container
     * 
     * @param  string $id 
     * @param  array $params 
     * @param  array $attribs 
     * @return void
     */
    public function captureStart($id, array $params = array(), array $attribs = array())
    {
        if (array_key_exists($id, $this->_captureLock)) {
            require_once 'Zend/Dojo/View/Exception.php';
            throw new  DojoViewException (sprintf('Lock already exists \for id "%s"', $id));
        }

        $this->_captureLock[$id] = true;
        $this->_captureInfo[$id] = array(
            'params'  => $params,
            'attribs' => $attribs,
        );

        return ob_start();
    }

    /**
     * Finish capturing content \for layout container
     * 
     * @param  string $id 
     * @return string
     */
    public function captureEnd($id)
    {
        if (!array_key_exists($id, $this->_captureLock)) {
            require_once 'Zend/Dojo/View/Exception.php';
            throw new  DojoViewException (sprintf('No capture lock exists \for id "%s"; nothing to capture', $id));
        }

        $content = ob_get_clean();
        extract($this->_captureInfo[$id]);
        unset($this->_captureLock[$id], $this->_captureInfo[$id]);
        return $this->_createLayoutContainer($id, $content, $params, $attribs);
    }
}
