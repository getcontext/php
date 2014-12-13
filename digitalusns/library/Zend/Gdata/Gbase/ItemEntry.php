<?php

namespace Zend\Gdata\Gbase;



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
require_once 'Zend/Gdata/Gbase/Entry.php';

/**
 * Concrete class \for working with Item entries.
 *
 * @link http://code.google.com/apis/base/
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Gbase\Extension\BaseAttribute as BaseAttribute;
use Zend\Gdata\App\InvalidArgumentException as GdataAppInvalidArgumentException;
use Zend\Gdata\Gbase\Extension\ItemType as ItemType;
use Zend\Gdata\Gbase\Entry as Entry;
use Zend\Gdata\App as App;




class  ItemEntry  extends  Entry 
{
    /**
     * The classname \for individual item entry elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\Gbase\ItemEntry';

    /**
     * Set the value \of the itme_type
     *
     * @param  ItemType  $value The desired value \for the item_type 
     * @return  ItemEntry  Provides a fluent interface
     */
    public function setItemType($value)
    {
        $this->addGbaseAttribute('item_type', $value, 'text');
        return $this;
    }

    /**
     * Adds a custom attribute to the entry in the following format:
     * &lt;g:[$name] type='[$type]'&gt;[$value]&lt;/g:[$name]&gt;      
     *
     * @param string $name The name \of the attribute
     * @param string $value The text value \of the attribute
     * @param string $type (optional) The type \of the attribute.
     *          e.g.: 'text', 'number', 'floatUnit'
     * @return  ItemEntry  Provides a fluent interface
     */
    public function addGbaseAttribute($name, $text, $type = null) {
        $newBaseAttribute =  new  BaseAttribute ($name, $text, $type);
        $this->_baseAttributes[] = $newBaseAttribute;
        return $this;
    }

    /**
     * Removes a Base attribute from the current list \of Base attributes
     * 
     * @param  BaseAttribute  $baseAttribute The attribute to be removed
     * @return  ItemEntry  Provides a fluent interface
     */
    public function removeGbaseAttribute($baseAttribute) {
        $baseAttributes = $this->_baseAttributes;
        \for ($i = 0; $i < count($this->_baseAttributes); $i++) {
            if ($this->_baseAttributes[$i] == $baseAttribute) {
                array_splice($baseAttributes, $i, 1);
                break;
            }
        }
        $this->_baseAttributes = $baseAttributes;
        return $this;
    }

    /**
     * Uploads changes in this entry to the server using  App 
     *
     * @param boolean $dryRun Whether the transaction is dry run or not
     * @return  App _Entry The updated entry
     * @throws  App _Exception
     */
    public function save($dryRun = false)
    {
        $uri = null;

        if ($dryRun == true) {
            $editLink = $this->getEditLink();
            if ($editLink !== null) {
                $uri = $editLink->getHref() . '?dry-run=true';
            }
            if ($uri === null) {
                require_once 'Zend/Gdata/App/InvalidArgumentException.php';
                throw new  GdataAppInvalidArgumentException ('You must specify an URI which needs deleted.');
            }
            $service = new  App ($this->getHttpClient());
            return $service->updateEntry($this, $uri);
        } else {
            parent::save();
        }
    }

    /**
     * Deletes this entry to the server using the referenced
     * \Zend\Http\Client to do a HTTP DELETE to the edit link stored in this
     * entry's link collection.
     *
     * @param boolean $dyrRun Whether the transaction is dry run or not
     * @return void
     * @throws  App _Exception
     */
    public function delete($dryRun = false)
    {
        $uri = null;

        if ($dryRun == true) {
            $editLink = $this->getEditLink();
            if ($editLink !== null) {
                $uri = $editLink->getHref() . '?dry-run=true';
            }
            if ($uri === null) {
                require_once 'Zend/Gdata/App/InvalidArgumentException.php';
                throw new  GdataAppInvalidArgumentException ('You must specify an URI which needs deleted.');
            }
            parent::delete($uri);
        } else {
            parent::delete();
        }
    }

}
