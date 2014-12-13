<?php

namespace DSF\Media;




use DSF\Filesystem\File as File;
use Zend\Config as Config;
use DSF\Media as Media;





class  Filetype  {
    public $key;
    public $type;
    public $mime;
    
    function __construct() {

    }
    
    static function load($filepath)
    {
         $fileExtension =  File ::getFileExtension($filepath);
         $fileExtension = strtolower($fileExtension);
         $allowedFiletypes =  Media ::getFiletypes();
         if(is_array($allowedFiletypes) && array_key_exists($fileExtension, $allowedFiletypes)) {
             return $allowedFiletypes[$fileExtension];
         }
         return null;
    }
    
    public function setFromConfigItem($key,  Config  $type)
    {
        $this->key = strtolower($key);
        $this->type = strtolower($type->type);
        $this->mime = strtolower($type->mime);
    }
    
    public function isType($mimeType)
    {
        if($mimeType == $this->mime) {
            return true;
        }else{
            return false;
        }
    }
}

?>
