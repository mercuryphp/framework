<?php

namespace System\Data\Entity\Attributes;

class Key {
    
    protected $key;
    
    /**
     * Initializes an instance of Key with a value that indicates that the entity
     * property is unique.
     * 
     * @param   string $key
     */
    public function __construct($key){
        $this->key = $key;
    }
    
    /**
     * Gets the key.
     * 
     * @return  string
     */
    public function getKeyName(){
        return $this->key;
    }
}