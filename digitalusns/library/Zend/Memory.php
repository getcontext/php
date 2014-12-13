<?php

namespace Zend;


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
 * @package     Memory 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  MemoryException  */
require_once 'Zend/Memory/Manager.php';

/**  MemoryException  */
require_once 'Zend/Memory/Exception.php';

/**  Memory _Value */
require_once 'Zend/Memory/Value.php';

/**  Memory _Container */
require_once 'Zend/Memory/Container.php';

/**  MemoryException  */
require_once 'Zend/Cache.php';


/**
 * @category   Zend
 * @package     Memory 
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Memory\Exception as MemoryException;
use Zend\Memory\Manager as Manager;
use Zend\Loader as Loader;
use Zend\Cache as Cache;




class  Memory 
{
    /**
     * Factory
     *
     * @param string $backend backend name
     * @param array $backendOptions associative array \of options \for the corresponding backend constructor
     * @return  Manager 
     * @throws  MemoryException 
     */
    public static function factory($backend, $backendOptions = array())
    {
        if (strcasecmp($backend, 'none') == 0) {
            return new  Manager ();
        }

        // because lowercase will fail
        $backend = @ucfirst(strtolower($backend));

        if (!in_array($backend,  Cache ::$availableBackends)) {
            throw new  MemoryException ("Incorrect backend ($backend)");
        }

        $backendClass = '\Zend\Cache\Backend_' . $backend;

        // For perfs reasons, we do not use the  Loader ::loadClass() method
        // (security controls are explicit)
        require_once str_replace('_', DIRECTORY_SEPARATOR, $backendClass) . '.php';

        $backendObject = new $backendClass($backendOptions);

        return new  Manager ($backendObject);
    }
}
