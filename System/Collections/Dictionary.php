<?php

namespace System\Collections;

class Dictionary extends Collection implements IDictionary{
    
    public function add($key, $value){
        if(array_key_exists($key, $this->collection)){
            throw new \InvalidArgumentException('An item with the same key has already been added.');
        }
        $this->collection[$key] = $value;
    }
    
    public function hasKey($key){
        if(array_key_exists($key, $this->collection)){
            return true;
        }
        return false;
    }
    
    public function toObject(){
        return json_decode(json_encode($this->collection), false);
    }
    
    public function set($key, $value){
        $this->collection[$key] = $value;
    }
    
    public function __set($key, $value){
        return $this->set($key, $value);
    }
    
    public function __get($key){
        return $this->get($key);
    }
}

?>
