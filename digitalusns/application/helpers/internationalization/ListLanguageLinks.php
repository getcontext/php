<?php

namespace DSF\View\Helper\Internationalization;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * ListLanguageLinks helper
 *
 * @uses viewHelper DSF_View_Helper_Internationalization
 */


use Zend\View\ViewInterface as ViewInterface;
use DSF\Language as Language;
use DSF\Builder as Builder;
use DSF\Uri as Uri;




class  ListLanguageLinks  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  
     */
    public function listLanguageLinks() {
        $page =  Builder ::getPage();
        $currentLanguage = $page->getLanguage();
        $availableLanguages = $page->getAvailableLanguages();
        $xhtml = "You are reading this page in " .  Language ::getFullName($currentLanguage) . '.';
        
        if(is_array($availableLanguages)) {
            $languageLinks = array();
            $uri = new  Uri ();
            $base = $uri->toString();
            foreach ($availableLanguages as $locale => $name) {
                if(!empty($locale) && $locale != $currentLanguage) {
                    $url = $base. '/p/lang/' . $locale;
                    $languageLinks[] = "<a href='" . $url . "'>" . $name . "</a>";
                }
            }
            
            if(is_array($languageLinks) && count($languageLinks) > 0) {
                $xhtml .= " This page is also translated into " . implode(", " , $languageLinks);
            }
        }

        return "<p>" . $xhtml . "</p>";
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
