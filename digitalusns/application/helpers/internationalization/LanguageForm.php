<?php

namespace DSF\View\Helper\Internationalization;


/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * SelectLanguage helper
 *
 * @uses viewHelper DSF_View_Helper_Internationalization
 */


use Zend\View\ViewInterface as ViewInterface;
use DSF\Filter\Post as Post;
use DSF\Language as Language;




class  LanguageForm  {
    
    /**
     * @var  ViewInterface  
     */
    public $view;
    
    /**
     *  this helper renders a language selector
     *  it also processes the selected language
     *  it must be rendered above the content in order \for the current 
     *  content to reflect the language selection
     */
    public function languageForm() {    
        //process form if this is a post back
        if( Post ::has('setLang')) {
             Language ::setLanguage($_POST['language']);
            // @todo: this needs to redirect so it loads the whole page in the new language
        }
        
        $currentLanguage =  Language ::getLanguage();
            
        $languageSelector = $this->view->selectLanguage('language',$currentLanguage);  
        $xhtml = "<form action='" . $this->view->ModuleAction() . "' method='post'>";
        $xhtml .= "<p>" . $languageSelector . "</p>";
        $xhtml .= "<p>" . $this->view->formSubmit('setLang', $this->view->GetTranslation('Set Language')) . "</p>";
        $xhtml .= "</form>";
        return $xhtml;
    }
    
    /**
     * Sets the view field 
     * @param $view  ViewInterface 
     */
    public function setView( ViewInterface  $view) {
        $this->view = $view;
    }
}
