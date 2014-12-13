<?php

namespace DSF;




use Zend\Controller\Front as Front;
use DSF\Filesystem\File as File;
use DSF\Toolbox\ToolboxString as ToolboxString;
use DSF\Filesystem\Dir as Dir;
use DSF\Media\Filetype as Filetype;
use Zend\Registry as Registry;





class  Media  {
    
    static function isAllowed($mimeType)
    {
        $filetypes = self::getFiletypes();
        foreach ($filetypes as $type) {
            if($type->isType($mimeType)) {
                return true;
            }
        }
        return false;
    }
    
    static function getFiletypes()
    {
        $config =  Registry ::get('config');
        $filetypes = $config->filetypes;
        if($filetypes) {
            foreach ($filetypes as $key => $filetype) {
                $type = new  Filetype ($key, $filetype);
                $type->setFromConfigItem($key, $filetype);
                $registeredFiletypes[$key] = $type;
            }
            return $registeredFiletypes;
        }
        return null;
    }
    
    static function upload($file, $path, $filename, $createPath = true, $base = '.')
    {
        
        if(self::isAllowed($file['type'])) {
            $path = self::getMediaPath($path);
    		//default to the name on the client machine
		    if($filename == null){$filename = $file['name'];}
		    $filename = str_replace('_','-',$filename);
		    $filename = str_replace(' ','-',$filename);
		    
		    $path = str_replace(self::rootDirectory(), '', $path);
		    $path =  ToolboxString ::stripUnderscores($path);
		    $path =  ToolboxString ::stripLeading('/', $path);
		    $path = $base . '/' . self::rootDirectory() . '/' . $path;

		    if($createPath)
		    {
		        //attempt to create the new path
		         Dir ::makeRecursive($base, $path);
		    }
		    
		    //clean the filename
		    $filename =  File ::cleanFilename($filename);
		    $filename = basename($filename);
		    $path .= "/" . $filename;
		    
		    if(move_uploaded_file($file['tmp_name'], $path))
		    {
		        //return the filepath if things worked out
		        //this is relative to the site root as this is the format that will be required \for links and what not
		        $fullPath =  ToolboxString ::stripLeading($base . '/', $path);
		        return $fullPath;
		    }
        }
    }
    
    static function batchUpload($files, $path, $filenames = array(), $createPath = true, $base = '.')
    {
        if(is_array($files)) {
            \for($i = 0; $i <= (count($files) - 1);$i++) {
                $file = array(
                    "name"      => $files["name"][$i],
                	"type"		=> $files["type"][$i],		
                    "tmp_name"	=> $files["tmp_name"][$i],
                    "error"		=> $files["error"][$i],
                    "size"		=> $files["size"][$i]
                );
                if(isset($filenames[$i])) {
                    $filename = true;
                }else{
                    $filename = null;
                }
                $result = self::upload($file, $path, $filename, $createPath, $base);
                if($result != null) {
                    $filepaths[] = $result;
                }
            }
            return $filepaths;
        }
        return false;
     }
     
     /**
      * this function renames a folder
      *
      * @param string $path - the full path
      * @param string $newName - the new name
      */
     static function renameFolder($path, $newName)
     {
         $path = self::getMediaPath($path);
         
	    //get the new name
        $parent =  ToolboxString ::getParentFromPath($path);
	    $newpath = $parent . '/' . $newName;

	    if( Dir ::rename($path, $newpath)) {
	        return $newpath;
	    }else{
	        return false;
	    }
     }
     
     static function deleteFolder($folder)
     {
        if(self::testFilepath($folder)) {
            $folder =  ToolboxString ::stripUnderscores($folder);
            $fullPath = self::rootDirectory() . '/' . $folder;
            
            //move the folder to the trash
             Dir ::copyRecursive($fullPath, $config->filepath->trash);
             Dir ::deleteRecursive($fullPath);
        }
     }
     
     static function deleteFile($file)
     {
         if(self::testFilepath($folder)) {
             $filepath =  ToolboxString ::stripUnderscores($file);
             $fullpath = self::rootDirectory() . '/' . $filepath;
             if(file_exists($fullpath)) {
                 unlink($fullpath);
             }
         }
     }
     
     static function testFilepath($filepath)
     {
         //dont allow access outside the media folder
        if(strpos($filepath, './') || strpos($filepath, './')) {
            throw new \Zend_Exception("Illegal file access attempt. Operation cancelled!");
            return false;
        }else{
            return true;
        }
     }
     
     static function getMediaPath($path, $relative = true)
     {
        $path =  ToolboxString ::stripUnderscores($path);
        
        //make it impossible to get out \of the media library
        $path = str_replace('./','',$path);
        $path = str_replace('../','',$path);
        
        //remove the reference to media if it exists
        $pathParts = explode('/', $path);
        if(is_array($pathParts)) {
            if($pathParts[0] == 'media') {
                unset($pathParts[0]);
            }

            //add the media root
            $path = self::rootDirectory($relative) . '/' . implode('/', $pathParts);
            return $path;
        }
	    return false;
     }
    
    static function rootDirectory($relative = true)
    {
        $config =  Registry ::get('config');
        $front =  Front ::getInstance();
        $baseUrl = $front->getBaseUrl();
        if($relative) {
            $prepend = '.';
        }
        return $prepend . $baseUrl . '/' . $config->filepath->media;
    }
}

?>
