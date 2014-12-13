<?php

namespace DSF\View\Helper\Form;




use Zend\view\viewInterface as viewInterface;
use view\viewInterface as viewInterface;




class  LinkList 
{

	/**
	 * comments
	 */
	public function LinkList($rowset, $linkTemplate, $replacements = array()){
		if(count($rowset) > 0){
		    foreach ($rowset as $row){
		        $link = $linkTemplate;
		        foreach ($replacements as $tag){
		            $link = str_replace('{' . $tag . '}', $row->$tag, $link);
		        }
		        $links[] = $link;
		    }
		    if(is_array($links)){
		        return $this->view->htmlList($links, null, null, false);
		    }
		}else{
		    return false;
		}
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
