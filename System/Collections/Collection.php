<?php

namespace System\Collections;

abstract class Collection implements \IteratorAggregate, \ArrayAccess{
    
    protected $collection = array();
    
    public function __construct(array $collection = array()){
        $this->collection = $collection;
    }
    
    public function count(){
        return count($this->collection);
    }
    
    public function clear(){
        $this->collection = array();
    }
    
    public function contains($value){
        if(array_search($value, $this->collection)){
            return true;
        }
        return false;
    }
    
    public function get($key){
        if(array_key_exists($key, $this->collection)){
            return $this->collection[$key];
        }
    }
    
    public function merge(array $array){
        $this->collection = array_merge($this->collection, $array);
        return $this;
    }

    public function remove($key){
        if(array_key_exists($key, $this->collection)){
            unset($this->collection[$key]);
        }
    }
    
    public function reverse(){
        $this->collection = array_reverse($this->collection);
    }
    
    public function sort(){
        asort($this->collection);
    }
    
    public function each(callable $func){
        $tmp = array();
        foreach($this->collection as $k=>$v){
            $tmp[$k] = $func($k, $v);
        }
        $this->collection = $tmp;
        return $this;
    }
    
    public function join($glue, $removeEmptyEntries = true){
        return \System\Std\String::join($glue, $this->collection, $removeEmptyEntries);
    }
    
    public function toArray(){
        return $this->collection;
    }

    public function getIterator(){
        return new \ArrayIterator($this->collection);
    }
    
    public function offsetExists($offset){
        if (array_key_exists($offset, $this->collection)){
            return true;
        }
        return false;
    }
    
    public function offsetGet($offset){
        return $this->collection[$offset];
    }
    
    public function offsetSet($offset, $value){
        $this->collection[$offset] = $value;
    }
    
    public function offsetUnset($offset){
        unset($this->collection[$offset]);
    }
}

?>
