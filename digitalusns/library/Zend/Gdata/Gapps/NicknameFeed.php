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
 * @see  Feed 
 */
require_once 'Zend/Gdata/Feed.php';

/**
 * @see \Zend\Gdata\Gapps\NicknameEntry
 */
require_once 'Zend/Gdata/Gapps/NicknameEntry.php';

/**
 * \Data model \for a collection \of Google Apps nickname entries, usually 
 * provided by the Google Apps servers.
 * 
 * For information on requesting this feed from a server, see the Google 
 * Apps service class, \Zend\Gdata\Gapps.
 *
 * @category   Zend
 * @package    \Zend\Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Gdata\Feed as Feed;




class  NicknameFeed  extends  Feed 
{
    
    protected $_entryClassName = '\Zend\Gdata\Gapps\NicknameEntry';
    protected $_feedClassName = '\Zend\Gdata\Gapps\NicknameFeed';
    
}
