<?php

namespace System\Configuration\Readers;

abstract class Reader {
    
    protected $config = array();
    
    public abstract function open($fileName);
    
    public function getItem($key){
        if(array_key_exists($key, $this->config)){
            return $this->config[$key];
        }
        return array();
    }
}
