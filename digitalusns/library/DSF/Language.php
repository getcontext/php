<?php

namespace DSF;



/**
 * this class is used to differentiate between the front end language and back end
 * this functionality should be revisited
 * @todo refactor this class to use \Zend\Translate
 *
 */


use Zend\Session\SessionNamespace as SessionNamespace;
use Zend\Registry as Registry;




class  Language  {
    const SESSION_KEY = 'currentLanguage';
    const LANGUAGE_KEY = 'current';

    static function setLanguage($language)
    {
        $session = self::getSession();
        $key = self::LANGUAGE_KEY;
        return $session->$key = $language;
    }
    
    static function getLanguage()
    {
        $session = self::getSession();
        $key = self::LANGUAGE_KEY;
        $currentLang = $session->$key;
        if(empty($currentLang)) {
            $config =  Registry ::get('config');
            $locale = $config->language->defaultLocale;
    	    $currentLang = $locale;
        }
        return $currentLang;        
    }
    
    static function getFullName($locale)
    {
        $config =  Registry ::get('config');
        $translations = $config->language->translations->toArray();
        if(isset($translations[$locale])) {
            return $translations[$locale];
        }
        return null;
    }
    
    static function getSession()
    {
        return new  SessionNamespace ('currentLanguage');
    }
}

?>
