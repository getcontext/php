<?php

namespace DSF\Layout\Grid;









abstract class  Div  {
    protected $_children = array();
    protected $_types = array(
        'unit' => 'Unit',
        'container' => 'Container'
    );
    protected $_columns;
    protected $_before;
    protected $_after;
    
    /**
     * 
     */
    function __construct() {
    
    }
    
    public function addUnit($cols, $before = 0, $after = 0)
    {
        
    }
    
}

?>
