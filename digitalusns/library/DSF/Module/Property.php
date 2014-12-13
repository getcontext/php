<?php

namespace DSF\Module;




use Zend\Controller\Front as Front;
use Zend\Config\Xml as ConfigXml;





class  Property  {
    
    function __construct() {
    
    }
    
    static function load($module)
    {
        $front =  Front ::getInstance();
        $modules = $front->getParam('cmsModules');
        $filepath = $front->getParam("pathToModules");
         
        if(isset($modules[$module])) {
            $propertiesFile = $filepath . '/' . $modules[$module] . '/properties.xml';    
            if(file_exists($propertiesFile)) {
                return new  ConfigXml ($propertiesFile);
            }    
        }
        
        return null;
    }
}

?>
