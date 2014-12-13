<?php

namespace DSF\View;




use Zend\Session\SessionNamespace as SessionNamespace;
use Zend\Registry as Registry;




class  Message 
{
    private $ns;
    private $message;
    
    function __construct()
    {
        
        if( Registry ::isRegistered('message')){
            $this->message =  Registry ::get('message');
        }else{
            $m = new  SessionNamespace ('message');
            if(isset($m->message)){
                $this->message = $m->message;
            }
        }
    }
    
    function clear()
    {
        unset($this->message);
        $this->updateNs();
    }
    
    function add($message)
    {
        $this->message = $message;
        $this->updateNs();
    }
    
    function hasMessage()
    {
        if($this->message){
            return true;
        }
    }
    
    function get()
    {
        return $this->message;
    }
    
    private function updateNs()
    {
        $m = new  SessionNamespace ('message');
        if(isset($this->message)){
             Registry ::set('message',$this->message);
            $m->message = $this->message;
        }else{
            unset($m->message);
        }
    }
}
