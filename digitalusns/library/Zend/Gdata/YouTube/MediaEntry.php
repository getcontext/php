<?php

namespace Zend\Gdata\YouTube;



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
 * @see \Zend\Gdata\Media
 */
require_once 'Zend/Gdata/Media.php';

/**
 * @see  Entry 
 */
require_once 'Zend/Gdata/Media/Entry.php';

/**
 * @see  MediaGroup 
 */
require_once 'Zend/Gdata/YouTube/Extension/MediaGroup.php';

/**
 * Represents the YouTube flavor \of a GData Media Entry
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\YouTube\Extension\MediaGroup as MediaGroup;
use Zend\Gdata\Media\Entry as Entry;




class  MediaEntry  extends  Entry 
{

    protected $_entryClassName = '\Zend\Gdata\YouTube\MediaEntry';

    /**
     * media:group element
     *
     * @var  MediaGroup 
     */
    protected $_mediaGroup = null;

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
        case $this->lookupNamespace('media') . ':' . 'group':
            $mediaGroup = new  MediaGroup ();
            $mediaGroup->transferFromDOM($child);
            $this->_mediaGroup = $mediaGroup;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

}
