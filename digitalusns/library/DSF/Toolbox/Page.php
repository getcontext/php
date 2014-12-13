<?php

namespace DSF\Toolbox;




use Zend\Db\Table\Row as Row;





class  Page  {

    static function getUrl( Row  $page, $separator = '/')
    {
        $labels[] = self::getLabel($page);
        $mdlPage = new \Page();
        $parents = $mdlPage->getParents($page);
        if(is_array($parents)) {
            foreach ($parents as $parent){
                $labels[] = self::getLabel($parent);
            }
        }
        
        if(is_array($labels)) {
            $labels = array_reverse($labels);
            return implode($separator, $labels);
        }
    }
    
    static function getLabel( Row  $page)
    {
        if(empty($page->label)) {
            return $page->name;
        }else{
            return $page->label;
        }
    }
}

?>
