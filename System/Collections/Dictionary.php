<?php

namespace System\Collections;

class Dictionary extends Collection implements IDictionary{
    
    public function add($key, $value){
        $this->readOnlyCheck();
        if(array_key_exists($key, $this->collection)){
            throw new \InvalidArgumentException('An item with the same key has already been added.');
        }
        $this->collection[$key] = $value;
        return $this;
    }
    
    public function toObject(){
        return json_decode(json_encode($this->collection), false);
    }
    
    public function set($key, $value){
        $this->readOnlyCheck();
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
