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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: PaginationControl.php 10349 2008-07-24 13:00:27Z norm2782 $
 */

/**
 * @category   Zend
 * @package    \Zend\View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\View\ViewInterface as ViewInterface;
use Zend\View\Exception as ViewException;
use Zend\Paginator as Paginator;




class  PaginationControl 
{
    /**
     * \View instance
     * 
     * @var \Zend\View_Instance
     */
    public $view = null;

    /**
     * Default view partial
     *
     * @var string
     */
    protected static $_defaultViewPartial = null;

    /**
     * Sets the view instance.
     *
     * @param   ViewInterface  $view \View instance
     * @return  PaginationControl 
     */
    public function setView( ViewInterface  $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Sets the default view partial.
     *
     * @param string $partial \View partial
     */
    public static function setDefaultViewPartial($partial)
    {
        self::$_defaultViewPartial = $partial;
    }
    
    /**
     * Gets the default view partial
     *
     * @return string
     */
    public static function getDefaultViewPartial()
    {
        return self::$_defaultViewPartial;
    }

    /**
     * Render the provided pages.  If no scrolling style or partial 
     * are specified, the defaults will be used (if set).
     *
     * @param   Paginator  $paginator
     * @param  string $scrollingStyle (Optional) Scrolling style
     * @param  string $partial (Optional) \View partial
     * @param  array|string $params (Optional) params to pass to the partial
     * @return string
     * @throws  ViewException 
     */
    public function paginationControl( Paginator  $paginator, $scrollingStyle = null, $partial = null, $params = null)
    {
        if (empty($partial)) {
            if (empty(self::$_defaultViewPartial)) {
                /**
                 * @see  ViewException 
                 */
                require_once 'Zend/View/Exception.php';
                
                throw new  ViewException ('No view partial provided and no default view partial set');
            }
            
            $partial = self::$_defaultViewPartial;
        }

        $pages = get_object_vars($paginator->getPages($scrollingStyle));
        
        if ($params != null) {
            $pages = array_merge($pages, (array) $params);
        }

        return $this->view->partial($partial, $pages);
    }
}
