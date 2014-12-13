<?php

namespace DSF\Content\Template;




use DSF\Content\Template as Template;
use DSF\Filesystem\Dir as Dir;
use Zend\Registry as Registry;




class  Loader 
{
    
    protected $_templatePath;
    const SYSTEM_FOLDER = "system";
    protected $_templates = null;
    
    public function __construct()
    {
        $config =  Registry ::get('config');
        $this->_templatePath = $config->filepath->contentTemplates;
    }
    
    public function getTemplates()
    {
        $templates =  Dir ::getDirectories($this->_templatePath);
        if(is_array($templates)) {
            foreach ($templates as $template) {
                if($template != self::SYSTEM_FOLDER ) {
                    $path = $this->_templatePath  . '/' . $template;
                    $subtemplates =  Dir ::getDirectories($path);
                    if(is_array($subtemplates)) {
                        foreach ($subtemplates as $subtemplate) {
                            $this->_templates[$template . '_' . $subtemplate] = ucwords($template . ' ' . $subtemplate);
                        }
                    }
                }
            }
        }
        return $this->_templates;
    }
    
    public function load($template)
    {
        $arrTemplate = explode('_', $template);
        if(is_array($arrTemplate) && count($arrTemplate) == 2) {
        	$folder = $arrTemplate[0];
        	$template = $arrTemplate[1];
        }else{
        	$folder = null;
        	$template = null;
        }
        return new  Template ($folder, $template, $this->_templatePath );
    }
}
