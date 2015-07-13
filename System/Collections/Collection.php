<?php

namespace System\Collections;

abstract class Collection implements \IteratorAggregate, \ArrayAccess{
    
    protected $collection = array();
    protected $isReadOnly = false;
    
    public function __construct(array $collection = array()){
        $this->collection = $collection;
    }
    
    public function count(){
        return count($this->collection);
    }
    
    public function clear(){
        $this->readOnlyCheck();
        $this->collection = array();
    }
    
    public function contains($value){
        if(array_search($value, $this->collection)){
            return true;
        }
        return false;
    }
    
    public function hasKey($key){
        if(array_key_exists($key, $this->collection)){
            return true;
        }
        return false;
    }
    
    public function get($key){
        if($this->hasKey($key)){
            return $this->collection[$key];
        }
    }
    
    public function last(){
        return end($this->collection);
    }


    public function merge($array){
        $this->readOnlyCheck();
        if($array instanceof \System\Collections\Collection){
            $array = $array->toArray();
        }
        $this->collection = array_merge($this->collection, $array);
        return $this;
    }

    public function remove($key){
        $this->readOnlyCheck();
        if(array_key_exists($key, $this->collection)){
            unset($this->collection[$key]);
            return true;
        }
        return false;
    }
    
    public function reverse(){
        $this->collection = array_reverse($this->collection);
    }
    
    public function sort(){
        asort($this->collection);
    }
    
    public function getKeys(){
        return new \System\Collections\ArrayList(array_keys($this->collection));
    }
    
    public function each(callable $func){
        $this->readOnlyCheck();
        $tmp = array();
        foreach($this->collection as $k=>$v){
            $tmp[$k] = $func($k, $v);
        }
        $this->collection = $tmp;
        return $this;
    }
    
    public function where($value){
        $this->readOnlyCheck();
        $tmp = array();
        foreach($this->collection as $item){
            if(is_scalar($item)){
                if($item == $value){
                    $tmp[] = $item;
                }
            }
        }
        $this->collection = $tmp;
        return $this;
    }
    
    public function like($regex){
        $this->readOnlyCheck();
        $tmp = array(); 
        foreach($this->collection as $item){
            if(is_scalar($item)){
                if(preg_match('@'.$regex.'@', $item)){
                    $tmp[] = $item;
                }
            }
        }
        $this->collection = $tmp;
        return $this;
    }
    
    public function join($glue, $removeEmptyEntries = true){
        return \System\Std\String::join($glue, $this->collection, $removeEmptyEntries);
    }
    
    public function isReadOnly($bool = null){
        if(is_null($bool)){
            return $this->isReadOnly;
        }
        $this->isReadOnly = $bool;
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
    
    protected function readOnlyCheck(){
        if($this->isReadOnly){
            throw new \Exception(get_called_class(). ' is read-only.');
        }
    }
}

?>
