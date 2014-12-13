<?php

namespace DSF\Installer;









class  Environment {
    
    function __construct() {
    
    }
    
    public function checkPhpVersion($requiredVersion)
    {
        if(version_compare(PHP_VERSION, $requiredVersion, '>')){
            return true;
        }else{
            return false;
        }
    }
    
    public function checkExtension($extension)
    {
        $extensions = get_loaded_extensions();
        if(in_array($extension, $extensions)) {
    	    return true;
    	}else{
    	    return false;
    	}      
    }
}

?>
