<?php

namespace Zend\Captcha;


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
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Word  */
require_once 'Zend/Captcha/Word.php';

/**
 * Example dumb word-based captcha
 * 
 * \Note that only rendering is necessary \for word-based captcha
 *  
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
*/


use Zend\Captcha\Word as Word;
use Zend\View as ZendView;




class  Dumb  extends  Word 
{
	/**
	 * Render the captcha
	 *
	 * @param   ZendView  $view
	 * @param  mixed $element
	 * @return string
	 */
	public function render( ZendView  $view, $element = null)
    {
        return 'Please type this word backwards: <b>'
             . strrev($this->getWord())
             . '</b>';
    }
}
