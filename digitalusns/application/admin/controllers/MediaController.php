<?php

namespace Admin;



/**
 * MediaZendController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';


use DSF\Filesystem\File as File;
use DSF\Toolbox\ToolboxString as ToolboxString;
use DSF\Filesystem\Dir as Dir;
use DSF\Filter\Post as Post;
use Zend\Registry as Registry;
use DSF\Media as Media;





class  MediaController  extends \Zend_Controller_Action {

    protected $_fullPathToMedia;
    protected $_pathToMedia;
    protected $_currentFolder;
    
    public function init()
    {
        $config =  Registry ::get('config');
        $this->_pathToMedia = $config->filepath->media;
        $this->_fullPathToMedia = $this->getFrontController()->getBaseUrl() . $this->_pathToMedia;
        $this->view->pathToMedia = $this->_pathToMedia;        
	    $this->view->breadcrumbs = array(
	       'Media' =>   $this->getFrontController()->getBaseUrl() . '/admin/media'
	    );
    }
    /**
     * The default action - show the home page
     */
    public function indexAction() {
        $this->view->path = "";
    }
    
    public function openFolderAction()
    {
        $folder = $this->_request->getParam('folder');
        $folder = str_replace('media_', '', $folder);
        
		$this->view->path = $folder;
        $folder =  ToolboxString ::stripHyphens($folder);
        
        $folder =  ToolboxString ::stripLeading("_", $folder);
        $folderArray = explode('_', $folder);
        
        if(is_array($folderArray)) {
            foreach ($folderArray as $pathPart) {
                if(!empty($pathPart)) {
                    $fullPathParts[] = $pathPart;
                    $fullPath = implode('_', $fullPathParts);
                    $folderPathParts[$fullPath] = $pathPart;
                }
            }
        }
        
        $this->view->folderPathParts = $folderPathParts;
        
        $pathToFolder = $this->_fullPathToMedia . '/' .  ToolboxString ::stripUnderscores($folder);
        $this->view->filesystemPath = $pathToFolder;
        $this->view->mediaPath = $folder;
		$this->view->folders =  Dir ::getDirectories($pathToFolder);
		$this->view->files =  File ::getFilesByType($pathToFolder,false,false,true);
		
		$this->view->breadcrumbs["Open Folder: " . $pathToFolder] = $this->getFrontController()->getBaseUrl() . "/admin/media/open-folder/folder/" . $folder;
	    $this->view->toolbarLinks = array();
	    
	    $tmpPath =  ToolboxString ::addUnderscores($folder);
	    $this->view->toolbarLinks[$this->view->GetTranslation('Add to my bookmarks')] = $this->getFrontController()->getBaseUrl() . '/admin/index/bookmark/url/admin_media_open-folder_folder_' . $tmpPath;
	    $this->view->toolbarLinks[$this->view->GetTranslation("Delete")] = $this->getFrontController()->getBaseUrl() . '/admin/media/delete-folder/folder/' . $folder;
		
    }
    
    public function createFolderAction()
    {
        $baseFolder =  Post ::get('path');
        $newFolder =  Post ::get('folder_name');
        
        //dont allow access outside the media folder
        if(strpos($baseFolder, './') || strpos($newFolder, './')) {
            throw new \Zend_Exception("Illegal file access attempt. Operation cancelled!");
        }
        
        $forwardPath = $baseFolder;
        if(!empty($newFolder)) {
            $fullPath = $this->_pathToMedia;
            
            $base = str_replace('media_', '', $baseFolder);
            
            if(!empty($base)) {
                $base =  ToolboxString ::stripUnderscores($base);
                $fullPath .= '/' . $base;
            }
            $fullPath .= '/' . $newFolder;
            
            if(!file_exists($fullPath)) {
                $result = @mkdir($fullPath, 0777);
            }else{
                $exists = true;
            }
            
            if($result || $exists) {
                $forwardPath .= '_' . $newFolder;
            }
        }
        
        $this->_request->setParam('folder', $forwardPath);
        $this->_forward('open-folder');
    }
    
    public function uploadAction()
    {
        $path =  Post ::get('filepath');
        $mediapath =  Post ::get('mediapath');
        $files = $_FILES['uploads'];
        if(is_array($files)) {
             Media ::batchUpload($files, $path);
        }
        $this->_request->setParam('folder', $mediapath);
          
        $this->_forward('open-folder');
    }
    
    public function deleteFolderAction()
    {        
        $folder = $this->_request->getParam("folder");
         Media ::deleteFolder($folder);        
        $folderPath =  ToolboxString ::stripUnderscores($folder);
        $parent =  ToolboxString ::getParentFromPath($folderPath);
        $cleanParent =  ToolboxString ::addUnderscores($parent);
        $this->_request->setParam('folder', $cleanParent);
        $this->_forward('open-folder');
    }
    
    public function deleteFileAction()
    {
        $file = $this->_request->getParam('file');
         Media ::deleteFile($file);      
        $filePath =  ToolboxString ::stripUnderscores($file);
        $parent =  ToolboxString ::getParentFromPath($filePath);
        $cleanParent =  ToolboxString ::addUnderscores($parent);
        $this->_request->setParam('folder', $cleanParent);
        $this->_forward('open-folder');
    }
    
    public function renameFolderAction()
    {
        $path =  Media ::renameFolder(
             Post ::get('filepath'),
             Post ::get('folder_name')
        );
        $path = str_replace('./','',$path);
        $path = str_replace('../','',$path);
        
        $folder =  ToolboxString ::addUnderscores($path);

        $this->_request->setParam('folder', $folder);
        $this->_forward('open-folder');
    }

}
?>

