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
 * @version    $Id: Mbox.php 9098 2008-03-30 19:29:10Z thomas $
 */


/**
 * @see  Folder 
 */
require_once 'Zend/Mail/Storage/Folder.php';

/**
 * @see  MailStorageFolderInterface 
 */
require_once 'Zend/Mail/Storage/Folder/Interface.php';

/**
 * @see  MailStorageMbox 
 */
require_once 'Zend/Mail/Storage/Mbox.php';


/**
 * @category   Zend
 * @package    \Zend\Mail
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Mail\Storage\Folder\FolderInterface as MailStorageFolderInterface;
use Zend\Mail\Storage\Exception as MailStorageException;
use Zend\Mail\Storage\Folder as Folder;
use Zend\Mail\Storage\Mbox as MailStorageMbox;




class  Mbox  extends  MailStorageMbox  implements  MailStorageFolderInterface 
{
    /**
     *  Folder  root folder \for folder structure
     * @var  Folder 
     */
    protected $_rootFolder;

    /**
     * rootdir \of folder structure
     * @var string
     */
    protected $_rootdir;

    /**
     * name \of current folder
     * @var string
     */
    protected $_currentFolder;

    /**
     * Create instance with parameters
     *
     * Disallowed parameters are:
     *   - filename use  MailStorageMbox  \for a single file
     * Supported parameters are:
     *   - dirname rootdir \of mbox structure
     *   - folder intial selected folder, default is 'INBOX'
     *
     * @param  $params array mail reader specific parameters
     * @throws  MailStorageException 
     */
    public function __construct($params)
    {
        if (is_array($params)) {
            $params = (object)$params;
        }

        if (isset($params->filename)) {
            /**
             * @see  MailStorageException 
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new  MailStorageException ('use  MailStorageMbox  \for a single file');
        }

        if (!isset($params->dirname) || !is_dir($params->dirname)) {
            /**
             * @see  MailStorageException 
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new  MailStorageException ('no valid dirname given in params');
        }

        $this->_rootdir = rtrim($params->dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->_buildFolderTree($this->_rootdir);
        $this->selectFolder(!empty($params->folder) ? $params->folder : 'INBOX');
        $this->_has['top']      = true;
        $this->_has['uniqueid'] = false;
    }

    /**
     * find all subfolders and mbox files \for folder structure
     *
     * Result is save in  Folder  instances with the root in $this->_rootFolder.
     * $parentFolder and $parentGlobalName are only used internally \for recursion.
     *
     * @param string $currentDir call with root dir, also used \for recursion.
     * @param  Folder |null $parentFolder used \for recursion
     * @param string $parentGlobalName used \for rescursion
     * @return null
     * @throws  MailStorageException 
     */
    protected function _buildFolderTree($currentDir, $parentFolder = null, $parentGlobalName = '')
    {
        if (!$parentFolder) {
            $this->_rootFolder = new  Folder ('/', '/', false);
            $parentFolder = $this->_rootFolder;
        }

        $dh = @opendir($currentDir);
        if (!$dh) {
            /**
             * @see  MailStorageException 
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new  MailStorageException ("can't read dir $currentDir");
        }
        while (($entry = readdir($dh)) !== false) {
            // ignore hidden files \for mbox
            if ($entry[0] == '.') {
                continue;
            }
            $absoluteEntry = $currentDir . $entry;
            $globalName = $parentGlobalName . DIRECTORY_SEPARATOR . $entry;
            if (is_file($absoluteEntry) && $this->_isMboxFile($absoluteEntry)) {
                $parentFolder->$entry = new  Folder ($entry, $globalName);
                continue;
            }
            if (!is_dir($absoluteEntry) /* || $entry == '.' || $entry == '..' */) {
                continue;
            }
            $folder = new  Folder ($entry, $globalName, false);
            $parentFolder->$entry = $folder;
            $this->_buildFolderTree($absoluteEntry . DIRECTORY_SEPARATOR, $folder, $globalName);
        }

        closedir($dh);
    }

    /**
     * get root folder or given folder
     *
     * @param string $rootFolder get folder structure \for given folder, else root
     * @return  Folder  root or wanted folder
     * @throws  MailStorageException 
     */
    public function getFolders($rootFolder = null)
    {
        if (!$rootFolder) {
            return $this->_rootFolder;
        }

        $currentFolder = $this->_rootFolder;
        $subname = trim($rootFolder, DIRECTORY_SEPARATOR);
        while ($currentFolder) {
            @list($entry, $subname) = @explode(DIRECTORY_SEPARATOR, $subname, 2);
            $currentFolder = $currentFolder->$entry;
            if (!$subname) {
                break;
            }
        }

        if ($currentFolder->getGlobalName() != DIRECTORY_SEPARATOR . trim($rootFolder, DIRECTORY_SEPARATOR)) {
            /**
             * @see  MailStorageException 
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new  MailStorageException ("folder $rootFolder not found");
        }
        return $currentFolder;
    }

    /**
     * select given folder
     *
     * folder must be selectable!
     *
     * @param  Folder |string $globalName global name \of folder or instance \for subfolder
     * @return null
     * @throws  MailStorageException 
     */
    public function selectFolder($globalName)
    {
        $this->_currentFolder = (string)$globalName;

        // getting folder from folder tree \for validation
        $folder = $this->getFolders($this->_currentFolder);

        try {
            $this->_openMboxFile($this->_rootdir . $folder->getGlobalName());
        } catch( MailStorageException  $e) {
            // check what went wrong
            if (!$folder->isSelectable()) {
                /**
                 * @see  MailStorageException 
                 */
                require_once 'Zend/Mail/Storage/Exception.php';
                throw new  MailStorageException ("{$this->_currentFolder} is not selectable");
            }
            // seems like file has vanished; rebuilding folder tree - but it's still an exception
            $this->_buildFolderTree($this->_rootdir);
            /**
             * @see  MailStorageException 
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new  MailStorageException ('seems like the mbox file has vanished, I\'ve rebuild the ' .
                                                         'folder tree, search \for an other folder and try again');
        }
    }

    /**
     * get  Folder  instance \for current folder
     *
     * @return  Folder  instance \of current folder
     * @throws  MailStorageException 
     */
    public function getCurrentFolder()
    {
        return $this->_currentFolder;
    }

    /**
     * magic method \for serialize()
     *
     * with this method you can cache the mbox class
     *
     * @return array name \of variables
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array('_currentFolder', '_rootFolder', '_rootdir'));
    }

    /**
     * magic method \for unserialize()
     *
     * with this method you can cache the mbox class
     *
     * @return null
     */
    public function __wakeup()
    {
        // if cache is stall selectFolder() rebuilds the tree on error
        parent::__wakeup();
    }
}
