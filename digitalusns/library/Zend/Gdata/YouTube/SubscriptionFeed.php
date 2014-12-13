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
 * @see  Feed 
 */
require_once 'Zend/Gdata/Media/Feed.php';

/**
 * @see  YouTube _SubscriptionEntry
 */
require_once 'Zend/Gdata/YouTube/SubscriptionEntry.php';

/**
 * The YouTube video subscription list flavor \of an Atom Feed with media support
 * Represents a list \of individual subscriptions, where each contained entry is
 * a subscription.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Media\Feed as Feed;
use Zend\Gdata\YouTube as YouTube;




class  SubscriptionFeed  extends  Feed 
{

    /**
     * The classname \for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = '\Zend\Gdata\YouTube\SubscriptionEntry';

    /**
     * Creates a Subscription feed, representing a list \of subscriptions,
     * usually associated with an individual user.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        foreach ( YouTube ::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

}
