<?php

namespace System\Data\Entity\Annotations;

class Key {
    
    protected $key;
    
    public function __construct($key){
        $this->key = $key;
    }
    
    public function getKeyName(){
        return $this->key;
    }
}

?>