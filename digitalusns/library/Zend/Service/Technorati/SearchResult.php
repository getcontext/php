<?php

namespace Zend\Service\Technorati;


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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: SearchResult.php 8064 2008-02-16 10:58:39Z thomas $
 */


/** 
 * @see  Result  
 */
require_once 'Zend/Service/Technorati/Result.php';


/**
 * Represents a single Technorati Search query result object. 
 * It is never returned as a standalone object, 
 * but it always belongs to a valid  SearchResult Set object.
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Service\Technorati\Result as Result;
use Zend\Service\Technorati\Utils as Utils;




class  SearchResult  extends  Result 
{
    /**
     * Technorati weblog object corresponding to queried keyword.
     * 
     * @var     \Zend\Service\Technorati\Weblog
     * @access  protected
     */
    protected $_weblog;

    /**
     * The title \of the entry.
     * 
     * @var     string
     * @access  protected
     */
    protected $_title;
    
    /**
     * The blurb from entry with search term highlighted.
     * 
     * @var     string
     * @access  protected
     */
    protected $_excerpt;

    /**
     * The datetime the entry was created.
     * 
     * @var     \Zend\Date
     * @access  protected
     */
    protected $_created;

    /**
     * The permalink \of the blog entry.
     * 
     * @var     \Zend\Uri\Http
     * @access  protected
     */
    protected $_permalink;
    

    /**
     * Constructs a new object object from DOM Element.
     *
     * @param   DomElement $dom the ReST fragment \for this object
     */
    public function __construct(DomElement $dom)
    {
        $this->_fields = array( '_permalink'    => 'permalink',
                                '_excerpt'      => 'excerpt',
                                '_created'      => 'created',
                                '_title'        => 'title');
        parent::__construct($dom);

        // weblog object field
        $this->_parseWeblog();

        // filter fields
        $this->_permalink =  Utils ::normalizeUriHttp($this->_permalink);
        $this->_created =  Utils ::normalizeDate($this->_created);
    }

    /**
     * Returns the weblog object that links queried URL.
     * 
     * @return  \Zend\Service\Technorati\Weblog
     */
    public function getWeblog() {
        return $this->_weblog;
    }
    
    /**
     * Returns the title \of the entry.
     * 
     * @return  string
     */
    public function getTitle() {
        return $this->_title;
    }
    
    /**
     * Returns the blurb from entry with search term highlighted.
     * 
     * @return  string
     */
    public function getExcerpt() {
        return $this->_excerpt;
    }
        
    /**
     * Returns the datetime the entry was created.
     * 
     * @return  \Zend\Date
     */
    public function getCreated() {
        return $this->_created;
    }
        
    /**
     * Returns the permalink \of the blog entry.
     * 
     * @return  \Zend\Uri\Http
     */
    public function getPermalink() {
        return $this->_permalink;
    }
    
}
