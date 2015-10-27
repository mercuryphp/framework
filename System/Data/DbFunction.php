<?php

namespace System\Data;

class DbFunction {
    
    protected $functionName;
    protected $args = array();
    
    public function __construct($functionName, array $args = array()){
        $this->functionName = $functionName;
        $this->args = $args;
    }
    
    public function getName(){
        return $this->functionName;
    }
    
    public function getArgs(){
        return $this->args;
    }
    
    public function toString(){
        return \System\Std\Str::set($this->functionName)
            ->append('(')
            ->glue(',', $this->args, true)
            ->append(')')
            ->toString();
    }
}