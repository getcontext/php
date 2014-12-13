<?php

namespace DSF\View\Helper\Internationalization;




use DSF\Language as Language;




class  GetCurrentLanguage 
{
  
    /**
     * this helper returns the current language
     *
     * @return unknown
     */
	public function GetCurrentLanguage()
	{
	    return  Language ::getLanguage();
	}
}
