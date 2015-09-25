<?php

namespace System\Collections;

abstract class Collection implements \IteratorAggregate, \ArrayAccess {
    
    protected $collection = array();
    protected $isReadOnly = false;
    
    /**
     * Initializes the collection with an array.
     * 
     * @param   array $collection = array()
     */
    public function __construct(array $collection = array()){
        $this->collection = $collection;
    }
    
    /**
     * Gets the number of elements in the collection.
     * 
     * @return  int
     */
    public function count(){
        return count($this->collection);
    }
    
    /**
     * Clears the collection.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @return  @this
     */
    public function clear(){
        $this->readOnlyCheck();
        $this->collection = array();
        return $this;
    }
    
    /**
     * Determines whether an element is in the collection.
     * 
     * @param   mixed $value
     * @return  bool
     */
    public function contains($value){
        if(in_array($value, $this->collection)){
            return true;
        }
        return false;
    }
    
    /**
     * Determines if the collection contains the specified key.
     * 
     * @param   mixed $key
     * @return  bool
     */
    public function hasKey($key){
        if(array_key_exists($key, $this->collection)){
            return true;
        }
        return false;
    }
    
    /**
     * Gets an element from the collection using the specified $key. If $default 
     * is specified and element is not found then get $default. 
     * 
     * @param   mixed $key
     * @param   mixed $default = null
     * @return  mixed
     */
    public function get($key, $default = null){
        if($this->hasKey($key)){
            return $this->collection[$key];
        }
        return $default;
    }
    
    /**
     * Gets the last element from the collection.
     * 
     * @return  mixed
     */
    public function last(){
        return end($this->collection);
    }

    /**
     * Merges an array or an instance of System.Collections.Collection with the collection.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $array
     * @return  mixed
     */
    public function merge($array){
        $this->readOnlyCheck();
        if($array instanceof \System\Collections\Collection){
            $array = $array->toArray();
        }
        $this->collection = array_merge($this->collection, $array);
        return $this;
    }

    /**
     * Removes the first occurrence of an element from the collection.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $value
     * @return  bool
     */
    public function remove($value){
        $this->readOnlyCheck();
        foreach($this->collection as $key=>$item){
            if($item == $value){
                unset($this->collection[$key]);
                return true;
            }
        }
        return false;
    }
    
    /**
     * Removes an element from the collection using the specified key.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $key
     * @return  bool
     */
    public function removeAt($key){
        $this->readOnlyCheck();
        if($this->hasKey($key)){
            unset($this->collection[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * Reverses the order of the elements in the collection.
     * 
     * @return  @this
     */
    public function reverse(){
        $this->collection = array_reverse($this->collection);
        return $this;
    }
    
    /**
     * Sorts the elements in the collection.
     * 
     * @return  @this
     */
    public function sort(){
        asort($this->collection);
        return $this;
    }
    
    /**
     * Gets an ArrayList of all keys in the collection.
     * 
     * @return  System.Collections.ArrayList
     */
    public function getKeys(){
        return new \System\Collections\ArrayList(array_keys($this->collection));
    }
    
    /**
     * Applies a callback function to all elements in the collection.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   callable $func
     * @return  @this
     */
    public function each(callable $func){
        $this->readOnlyCheck();
        foreach($this->collection as $k=>$v){
            $this->collection[$k] = $func($v, $k);
        }
        return $this;
    }
    
    /**
     * Filters the collection based on the condition specified in the callback.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   callable $callback
     * @return  @this
     */
    public function where(callable $callback){
        $this->readOnlyCheck();
        $tmp = array();
        foreach($this->collection as $k=>$v){
            $return = $callback($v, $k);
            if($return){
                $tmp = array_merge($tmp, $return);
            }
        }
        $this->collection = $tmp;
        unset($tmp);
        return $this;
    }
    
    /**
     * Filters the collection where all element values match the specified value and type.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   mixed $value
     * @return  @this
     */
    public function whereValue($value){
        $this->readOnlyCheck();
        $this->collection = array_filter($this->collection, function($v) use($value){
            if($value === $v){
                return $v;
            }
        });
        return $this;
    }

    /**
     * Filters the collection where all element values match the specified regex.
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @param   string $regex
     * @return  @this
     */
    public function likeValue($regex){
        $this->collection = array_filter($this->collection, function($v) use($regex){
            if(preg_match($regex, $v)){
                return $v;
            }
        });
        return $this;
    }
    
    /**
     * Gets a System.Std.String where all elements are join using the specified glue.
     * 
     * @param   string $glue
     * @param   bool $removeEmptyEntries = true
     * @return  System.Std.Str
     */
    public function join($glue, $removeEmptyEntries = true){
        return \System\Std\Str::join($glue, $this->collection, $removeEmptyEntries);
    }
    
    /**
     * Gets or sets a boolean value indicating if the collection is read-only.
     * 
     * @param   bool $bool = null
     * @return  mixed
     */
    public function isReadOnly($bool = null){
        if(is_null($bool)){
            return $this->isReadOnly;
        }
        $this->isReadOnly = $bool;
    }
    
    /**
     * Gets the internal PHP array.
     * 
     * @return  array
     */
    public function toArray(){
        return $this->collection;
    }

    /**
     * Gets an ArrayIterator.
     * 
     * @return  ArrayIterator
     */
    public function getIterator(){
        return new \ArrayIterator($this->collection);
    }
    
    /**
     * Gets a boolean value indicating if the collection offset exists.
     * This method is not intended to be used directly.
     * 
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
     * @param   mixed $offset
     * @return  void
     */
    public function offsetUnset($offset){
        unset($this->collection[$offset]);
    }

    /**
     * Throws ReadOnlyException if the collection is set as read-only.
     * 
     * @return  void
     */
    protected function readOnlyCheck(){
        if($this->isReadOnly){
            throw new ReadOnlyException(get_called_class(). ' is read-only.');
        }
    }
}