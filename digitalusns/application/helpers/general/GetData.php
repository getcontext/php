<?php
/**
 *
 * @author forrest lyman
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * getData helper
 *
 * @uses viewHelper DSF_View_Helper_General
 */
class \DSF_View_Helper_General_getData {
    
    /**
     * @var \Zend\View\ViewInterface 
     */
    public $view;
    
    /**
     *  
     */
    public function getData($field, $dataSet = null) {
        if(is_array($dataSet)) {
            if(isset($dataSet[$field])) {
                return $dataSet[$field];
            }
        }elseif (is_object($dataSet)) {
            if(isset($dataSet->$field)) {
                return $dataSet->$field;
            }
        }else{
            return $this->view->$field;
        }
    }
    
    /**
     * Sets the view field 
     * @param $view \Zend\View\ViewInterface
     */
    public function setView(\Zend\View\ViewInterface $view) {
        $this->view = $view;
    }
}
