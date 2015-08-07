<?php

namespace System\Data\Entity;

class DbListResult implements \IteratorAggregate, \ArrayAccess {
    
    protected $collection = array();
    
    public function __construct(array $result){
        $this->collection = $result;
    }
    
    public function each(callable $func){
        foreach($this->collection as $k=>$v){
            $this->collection[$k] = $func($v, $k);
        }
        return $this;
    }
    
    public function where(callable $func){
        $this->collection = array_filter($this->collection, $func);
        return $this;
    }
    
    public function groupBy($field){
        $tmp = array();
        foreach($this->collection as $v){
            $item = \System\Std\Object::getProperties($v);
            $tmp[$item[$field]] = $item;
        }
        $this->collection = array_values($tmp);
        unset($tmp);
        return $this;
    }
    
    public function sortBy($field){
        usort($this->collection, function($a, $b) use($field) {
            $array1 = \System\Std\Object::getProperties($a);
            $array2 = \System\Std\Object::getProperties($b);
            return $array1[$field] > $array2[$field];
        });
        return $this;
    }
    
    /**
     * Reverses the order of the elements in the collection.
     * 
     * @method  reverse
     * @return  $this
     */
    public function reverse(){
        $this->collection = array_reverse($this->collection);
        return $this;
    }
    
    public function limit($limit, $offset = 0){
        $this->collection = array_slice($this->collection, $offset, $limit);
        return $this;
    }
    
    public function sum($field){
        $sum = 0;
        foreach($this->collection as $v){
            $item = \System\Std\Object::getProperties($v);
            $sum += (int)$item[$field];
        }
        return $sum;
    }
    
    public function avg($field){
        $sum = 0;
        foreach($this->collection as $v){
            $item = \System\Std\Object::getProperties($v);
            $sum += (int)$item[$field];
        }
        return $sum / count($this->collection);
    }
    
    public function min($field){
        $this->sortBy($field);

        if(isset($this->collection[0])){
            $item = \System\Std\Object::getProperties($this->collection[0]);
            return $item[$field];
        }
    }
    
    public function max($field){
        $this->sortBy($field);
        $count = $this->count()-1;
        if(isset($this->collection[$count])){
            $item = \System\Std\Object::getProperties($this->collection[$count]);
            return $item[$field];
        }
    }
    
    public function count(){
        return count($this->collection);
    }

    /**
     * Gets an ArrayIterator.
     * 
     * @method  getIterator
     * @return  ArrayIterator
     */
    public function getIterator(){
        return new \ArrayIterator($this->collection);
    }
    
    /**
     * Gets a boolean value indicating if the collection offset exists.
     * This method is not intended to be used directly.
     * 
     * @method  offsetExists
     * @param   mixed $offset
     * @return  bool
     */
    public function offsetExists($offset){
        if (array_key_exists($offset, $this->collection)){
            return true;
        }
        return false;
    }
    
    /**
     * Gets an element from the collection using an offset.
     * This method is not intended to be used directly.
     * 
     * @method  offsetGet
     * @param   mixed $offset
     * @return  mixed
     */
    public function offsetGet($offset){
        return $this->collection[$offset];
    }
    
    /**
     * Sets an element in the collection using an offset.
     * This method is not intended to be used directly.
     * 
     * @method  offsetSet
     * @param   mixed $offset
     * @param   mixed $value
     * @return  mixed
     */
    public function offsetSet($offset, $value){
        $this->collection[$offset] = $value;
    }
    
    /**
     * Removes an element from the collection using an offset.
     * This method is not intended to be used directly.
     * 
     * @method  offsetUnset
     * @param   mixed $offset
     * @return  void
     */
    public function offsetUnset($offset){
        unset($this->collection[$offset]);
    }
}
