<?php

namespace System\Web\Mvc;

class StringResult extends ActionResult {
    
    protected $string = '';
    
    public function __construct($string){
        if(is_scalar($string)){
            $this->string = $string;
        }else{
            $this->string = serialize($string);
        }
    }
    
    public function execute(){
        return $this->string;
    }
}

?>