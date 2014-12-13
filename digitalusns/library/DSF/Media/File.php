<?php

namespace DSF\Media;




use DSF\Media\Filetype as Filetype;
use DSF\Media as Media;





class  File  {
    public $name;
    public $path;
    public $type;
    public $fullUrl;
    public $fullPath;
    public $exists = false;
    
    function __construct($path, $basePath = './', $baseUrl = '/') {
        $this->name = basename($path);
        $this->path = $path;
        
        $mediaFolder =  Media ::rootDirectory();
        
        $this->fullPath = $basePath . $mediaFolder . '/' . $path;
        $this->fullUrl = $baseUrl . $mediaFolder . '/' . $path;
        
        $this->type =  Filetype ::load($path);
        
        if($this->fileExists()) {
            $this->exists = true;
        }
    }
    
    public function fileExists()
    {
        if(file_exists($this->fullPath)) {
            return true;
        }
        return false;
    }
    
}

?>
