<?php

namespace Zend\Mail\Storage\Folder;


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
 * @package    \Zend\Mail
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 9098 2008-03-30 19:29:10Z thomas $
 */


/**
 * @category   Zend
 * @package    \Zend\Mail
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */






interface  FolderInterface 
{
    /**
     * get root folder or given folder
     *
     * @param string $rootFolder get folder structure \for given folder, else root
     * @return \Zend\Mail\Storage\Folder root or wanted folder
     */
    public function getFolders($rootFolder = null);

    /**
     * select given folder
     *
     * folder must be selectable!
     *
     * @param \Zend\Mail\Storage\Folder|string $globalName global name \of folder or instance \for subfolder
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function selectFolder($globalName);


    /**
     * get \Zend\Mail\Storage\Folder instance \for current folder
     *
     * @return \Zend\Mail\Storage\Folder instance \of current folder
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getCurrentFolder();
}
