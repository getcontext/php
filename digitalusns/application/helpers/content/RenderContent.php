<?php

namespace DSF\View\Helper\Content;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  RenderContent 
{
	public function RenderContent($block, $rowset = null, $wordCount = 0){
        if($rowset == null){
           $content = $this->view->page->getContent();
        }else{
            $content = $rowset;
        }
        
        $xhtml = '';
        
        if($wordCount > 0){
          $xhtml .= $this->view->TruncateText($content->$block, $wordCount); 
        }else{
          $xhtml .= $content->$block;  
        }
            	    
    	return stripslashes($xhtml);
	}
		
    /**
     * Set this->view object
     *
     * @param  Zend_this-> viewInterface  $this->view
     * @return Zend_this->view_Helper_DeclareVars
     */
    public function setview( viewInterface  $view)
    {
        $this->view = $view;
        return $this;
    }
}
