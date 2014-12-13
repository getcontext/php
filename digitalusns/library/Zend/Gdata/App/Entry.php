<?php

namespace Zend\Gdata\App;



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
 * @see  FeedEntryParent 
 */
require_once 'Zend/Gdata/App/FeedEntryParent.php';

/**
 * @see  Content 
 */
require_once 'Zend/Gdata/App/Extension/Content.php';

/**
 * @see  Published 
 */
require_once 'Zend/Gdata/App/Extension/Published.php';

/**
 * @see  Source 
 */
require_once 'Zend/Gdata/App/Extension/Source.php';

/**
 * @see  Summary 
 */
require_once 'Zend/Gdata/App/Extension/Summary.php';

/**
 * @see  Control 
 */
require_once 'Zend/Gdata/App/Extension/Control.php';

/**
 * Concrete class \for working with Atom entries.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\App\Extension\Published as Published;
use Zend\Gdata\App\Extension\Content as Content;
use Zend\Gdata\App\Extension\Summary as Summary;
use Zend\Gdata\App\Extension\Control as Control;
use Zend\Gdata\App\Extension\Source as Source;
use Zend\Gdata\App\FeedEntryParent as FeedEntryParent;
use Zend\Gdata\App as App;




class  Entry  extends  FeedEntryParent 
{

    /**
     * Root XML element \for Atom entries.
     *
     * @var string
     */
    protected $_rootElement = 'entry';

    /**
     * Class name \for each entry in this feed*
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\App\Entry';

    /**
     * atom:content element
     *
     * @var  Content 
     */
    protected $_content = null;

    /**
     * atom:published element
     *
     * @var  Published 
     */
    protected $_published = null;

    /**
     * atom:source element
     *
     * @var  Source 
     */
    protected $_source = null;

    /**
     * atom:summary element
     *
     * @var  Summary 
     */
    protected $_summary = null;

    /**
     * app:control element
     *
     * @var  Control 
     */
    protected $_control = null;

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_content != null) {
            $element->appendChild($this->_content->getDOM($element->ownerDocument));
        }
        if ($this->_published != null) {
            $element->appendChild($this->_published->getDOM($element->ownerDocument));
        }
        if ($this->_source != null) {
            $element->appendChild($this->_source->getDOM($element->ownerDocument));
        }
        if ($this->_summary != null) {
            $element->appendChild($this->_summary->getDOM($element->ownerDocument));
        }
        if ($this->_control != null) {
            $element->appendChild($this->_control->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'content':
            $content = new  Content ();
            $content->transferFromDOM($child);
            $this->_content = $content;
            break;
        case $this->lookupNamespace('atom') . ':' . 'published':
            $published = new  Published ();
            $published->transferFromDOM($child);
            $this->_published = $published;
            break;
        case $this->lookupNamespace('atom') . ':' . 'source':
            $source = new  Source ();
            $source->transferFromDOM($child);
            $this->_source = $source;
            break;
        case $this->lookupNamespace('atom') . ':' . 'summary':
            $summary = new  Summary ();
            $summary->transferFromDOM($child);
            $this->_summary = $summary;
            break;
        case $this->lookupNamespace('app') . ':' . 'control':
            $control = new  Control ();
            $control->transferFromDOM($child);
            $this->_control = $control;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Uploads changes in this entry to the server using  App 
     *
     * @return  Entry  The updated entry
     * @throws  App _Exception
     */
    public function save()
    {
        $service = new  App ($this->getHttpClient());
        return $service->updateEntry($this);
    }

    /**
     * Deletes this entry to the server using the referenced
     * \Zend\Http\Client to do a HTTP DELETE to the edit link stored in this
     * entry's link collection.
     *
     * @return void
     * @throws  App _Exception
     */
    public function delete()
    {
        $service = new  App ($this->getHttpClient());
        $service->delete($this);
    }

    /**
     * Gets the value \of the atom:content element
     *
     * @return  Content 
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the value \of the atom:content element
     *
     * @param  Content  $value
     * @return  Entry  Provides a fluent interface
     */
    public function setContent($value)
    {
        $this->_content = $value;
        return $this;
    }

    /**
     * Sets the value \of the atom:published element
     * This represents the publishing date \for an entry
     *
     * @return  Published 
     */
    public function getPublished()
    {
        return $this->_published;
    }

    /**
     * Sets the value \of the atom:published element
     * This represents the publishing date \for an entry
     *
     * @param  Published  $value
     * @return  Entry  Provides a fluent interface
     */
    public function setPublished($value)
    {
        $this->_published = $value;
        return $this;
    }

    /**
     * Gets the value \of the atom:source element
     *
     * @return  Source 
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Sets the value \of the atom:source element
     *
     * @param  Source  $value
     * @return  Entry  Provides a fluent interface
     */
    public function setSource($value)
    {
        $this->_source = $value;
        return $this;
    }

    /**
     * Gets the value \of the atom:summary element
     * This represents a textual summary \of this entry's content
     *
     * @return  Summary 
     */
    public function getSummary()
    {
        return $this->_summary;
    }

    /**
     * Sets the value \of the atom:summary element
     * This represents a textual summary \of this entry's content
     *
     * @param  Summary  $value
     * @return  Entry  Provides a fluent interface
     */
    public function setSummary($value)
    {
        $this->_summary = $value;
        return $this;
    }

    /**
     * Gets the value \of the app:control element
     *
     * @return  Control 
     */
    public function getControl()
    {
        return $this->_control;
    }

    /**
     * Sets the value \of the app:control element
     *
     * @param  Control  $value
     * @return  Entry  Provides a fluent interface
     */
    public function setControl($value)
    {
        $this->_control = $value;
        return $this;
    }
}
