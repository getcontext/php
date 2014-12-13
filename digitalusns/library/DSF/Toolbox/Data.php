<?php

namespace DSF\Toolbox;









class  Data  {
    static function getValueOrNull($value)
    {
        if(empty($value)) {
            return null;
        }else{
            return $value;
        }
    }
}

?>
