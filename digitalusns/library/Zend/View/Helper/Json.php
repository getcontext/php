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
 * @version    $Id: Json.php 10665 2008-08-05 10:57:18Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  ZendJson  */
require_once 'Zend/Json.php';

/**  Front  */
require_once 'Zend/Controller/Front.php';

/**  ViewHelperAbstract .php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper \for simplifying JSON responses
 *
 * @package    \Zend\View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\Helper\HelperAbstract as ViewHelperAbstract;
use Zend\Controller\Front as Front;
use Zend\Layout as Layout;
use Zend\Json as ZendJson;




class  Json  extends  ViewHelperAbstract 
{
    /**
     * Encode data as JSON, disable layouts, and set response header
     *
     * If $keepLayouts is true, does not disable layouts.
     * 
     * @param  mixed $data 
     * @param  bool $keepLayouts
     * @return string|void
     */
    public function json($data, $keepLayouts = false)
    {
        $data =  ZendJson ::encode($data);
        if (!$keepLayouts) {
            require_once 'Zend/Layout.php';
            $layout =  Layout ::getMvcInstance();
            if ($layout instanceof  Layout ) {
                $layout->disableLayout();
            }
        }

        $response =  Front ::getInstance()->getResponse();
        $response->setHeader('Content-Type', 'application/json');
        return $data;
    }
}
