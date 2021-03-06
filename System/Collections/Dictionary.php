<?php

namespace System\Collections;

class Dictionary extends Collection {
    
    /**
     * Adds an element to the collection using a key.
     * Throws ReadOnlyException if the collection is set as read-only.
     * Throws InvalidArgumentException if the key already exists.
     * 
     * @param   mixed $key
     * @param   mixed $value
     * @return  @this
     */
    public function add($key, $value){
        $this->readOnlyCheck();
        if($this->hasKey($key)){
            throw new \InvalidArgumentException('An item with the same key has already been added.');
        }
        $this->collection[$key] = $value;
        return $this;
    }
    
    /**
     * Adds an element to the collection using a unique key. The element is not
     * added if the key already exists.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $key
     * @param   mixed $value
     * @return  @this
     */
    public function addUnique($key, $value){
        $this->readOnlyCheck();
        if(!$this->hasKey($key)){
            $this->collection[$key] = $value;
        }
        return $this;
    }

    /**
     * Adds or replaces an element in the collection using a key.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $key
     * @param   mixed $value
     * @return  @this
     */
    public function set($key, $value){
        $this->readOnlyCheck();
        $this->collection[$key] = $value;
        return $this;
    }
    
    /**
     * Gets an stdClass object of the collection.
     * 
     * @return  stdClass
     */
    public function toObject(){
        return json_decode(json_encode($this->collection), false);
    }
    
    /**
     * Magic method. Dynamically creates collection key/value elements.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $key
     * @param   mixed $value
     * @return  @this
     */
    public function __set($key, $value){
        return $this->set($key, $value);
    }
    
    /**
     * Magic method. Dynamically gets elements from the collection using the specified key.
     * 
     * @param   mixed $key
     * @return  mixed
     */
    public function __get($key){
        return $this->get($key);
    }
}