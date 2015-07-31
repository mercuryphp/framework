<?php

namespace System\Configuration\Readers;

abstract class Reader {
    
    protected $config = array();
    
    public abstract function open($fileName);
    
    /**
     * Gets an element from the configuration using a specified $key.
     * 
     * @method  getItem
     * @param   string $key
     * @return  mixed
     */
    public function getItem($key){
        if(array_key_exists($key, $this->config)){
            return $this->config[$key];
        }
        return array();
    }
}
