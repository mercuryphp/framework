<?php

namespace System\Collections;

class Dictionary extends Collection {
    
    /**
     * Adds an element to the collection using a key.
     * Throws ReadOnlyException if the collection is set as read-only.
     * Throws InvalidArgumentException if the key already exists.
     * 
     * @method  add
     * @param   mixed $key
     * @param   mixed $value
     * @return  System.Collections.Dictionary
     */
    public function add($key, $value){
        $this->readOnlyCheck();
        if(array_key_exists($key, $this->collection)){
            throw new \InvalidArgumentException('An item with the same key has already been added.');
        }
        $this->collection[$key] = $value;
        return $this;
    }
    
    /**
     * Gets an stdClass object of the collection.
     * 
     * @method  toObject
     * @return  stdClass
     */
    public function toObject(){
        return json_decode(json_encode($this->collection), false);
    }
    
    /**
     * Adds or replaces an element in the collection using a key.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @method  set
     * @param   mixed $key
     * @param   mixed $value
     * @return  System.Collections.Dictionary
     */
    public function set($key, $value){
        $this->readOnlyCheck();
        $this->collection[$key] = $value;
        return $this;
    }
    
    /**
     * Magic method. Dynamically creates collection key/value elements.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @method  __set
     * @param   mixed $key
     * @param   mixed $value
     * @return  System.Collections.Dictionary
     */
    public function __set($key, $value){
        return $this->set($key, $value);
    }
    
    /**
     * Magic method. Dynamically gets elements from the collection using the specified key.
     * 
     * @method  __get
     * @param   mixed $key
     * @return  mixed
     */
    public function __get($key){
        return $this->get($key);
    }
}