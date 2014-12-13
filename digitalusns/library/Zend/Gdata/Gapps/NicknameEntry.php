<?php

namespace Zend\Gdata\Gapps;



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
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see  Entry 
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see  Login 
 */
require_once 'Zend/Gdata/Gapps/Extension/Login.php';

/**
 * @see  Nickname 
 */
require_once 'Zend/Gdata/Gapps/Extension/Nickname.php';

/**
 * \Data model class \for a Google Apps Nickname Entry.
 * 
 * Each nickname entry describes a single nickname within a Google Apps 
 * hosted domain. Each user may own several nicknames, but each nickname may 
 * only belong to one user. Multiple entries are contained within instances 
 * \of  Gapps _NicknameFeed.
 * 
 * To transfer nickname entries to and from the Google Apps servers, 
 * including creating new entries, refer to the Google Apps service class,
 *  Gapps .
 *
 * This class represents <atom:entry> in the Google \Data protocol.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Gapps\Extension\Nickname as Nickname;
use Zend\Gdata\Gapps\Extension\Login as Login;
use Zend\Gdata\Entry as Entry;
use Zend\Gdata\Gapps as Gapps;




class  NicknameEntry  extends  Entry 
{

    protected $_entryClassName = '\Zend\Gdata\Gapps\NicknameEntry';

    /**
     * <apps:login> element used to hold information about the owner 
     * \of this nickname, including their username.
     * 
     * @var  Login 
     */
    protected $_login = null;
    
    /**
     * <apps:nickname> element used to hold the name \of this nickname.
     * 
     * @var  Nickname 
     */
    protected $_nickname = null;
    
    /**
     * Create a new instance.
     * 
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        foreach ( Gapps ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text \for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     *          child properties.
     */
    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_login !== null) {
            $element->appendChild($this->_login->getDOM($element->ownerDocument));
        }
        if ($this->_nickname !== null) {
            $element->appendChild($this->_nickname->getDOM($element->ownerDocument));
        }
        return $element;
    }
    
    /**
     * Creates individual Entry objects \of the appropriate type and
     * stores them as members \of this entry based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('apps') . ':' . 'login'; 
                $login = new  Login ();
                $login->transferFromDOM($child);
                $this->_login = $login;
                break;
            case $this->lookupNamespace('apps') . ':' . 'nickname'; 
                $nickname = new  Nickname ();
                $nickname->transferFromDOM($child);
                $this->_nickname = $nickname;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * Get the value \of the login property \for this object.
     *
     * @see setLogin
     * @return  Login  The requested object.
     */
    public function getLogin()
    {
        return $this->_login;
    }

    /**
     * Set the value \of the login property \for this object. This property 
     * is used to store the username address \of the current user.
     * 
     * @param  Login  $value The desired value \for 
     *          this instance's login property.
     * @return  NicknameEntry  Provides a fluent interface.
     */
    public function setLogin($value)
    {
        $this->_login = $value;
        return $this;
    }

    /**
     * Get the value \of the nickname property \for this object.
     *
     * @see setNickname
     * @return  Nickname  The requested object.
     */
    public function getNickname()
    {
        return $this->_nickname;
    }

    /**
     * Set the value \of the nickname property \for this object. This property 
     * is used to store the the name \of the current nickname.
     * 
     * @param  Nickname  $value The desired value \for 
     *          this instance's nickname property.
     * @return  NicknameEntry  Provides a fluent interface.
     */
    public function setNickname($value)
    {
        $this->_nickname = $value;
        return $this;
    }

}
