<?php

namespace System\Collections;

class ArrayList extends Collection {
    
    /**
     * Adds an element to the end of the collection.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @method  add
     * @param   mixed $value
     * @return  System.Collections.ArrayList
     */
    public function add($value){
        $this->readOnlyCheck();
        $this->collection[] = $value;
        return $this;
    }
    
    /**
     * Inserts an element into the collection at the specified index. 
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @method  insert
     * @param   int $index
     * @param   mixed $value
     * @return  System.Collections.ArrayList
     */
    public function insert($index, $value){
        $this->readOnlyCheck();
        return new ArrayList(array_splice($this->collection, $index, 0, $value));
    }
    
    /**
     * Returns an ArrayList whose elements are copies of the specified value.
     * 
     * @method  repeat
     * @param   mixed $value
     * @param   int $count
     * @return  System.Collections.ArrayList
     */
    public static function repeat($value, $count){
        return new ArrayList(array_fill(0, $count, $value));
    }
}