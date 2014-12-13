<?php

namespace Zend\Gdata\Docs;



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
 * @see  Feed 
 */
require_once 'Zend/Gdata/Feed.php';


/**
 * \Data model \for a Google Documents List feed \of documents
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Feed as Feed;
use Zend\Gdata\Docs as Docs;




class  DocumentListFeed  extends  Feed 
{

    /**
     * The classname \for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\Docs\DocumentListEntry';

    /**
     * The classname \for the feed.
     *
     * @var string
     */
    protected $_feedClassName = '\Zend\Gdata\Docs\DocumentListFeed';

    /**
     * Create a new instance \of a feed \for a list \of documents.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        foreach ( Docs ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

}
