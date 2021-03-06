<?php

namespace System\Data\Entity;

class DbListResult implements \IteratorAggregate, \ArrayAccess {
    
    protected $collection = array();
    
    /**
     * Initializes an instance of DbListResult with the results of a database 
     * query that returns more than one row.
     * 
     * @param   array $result
     */
    public function __construct(array $result){
        $this->collection = $result;
    }
    
    public function rowKey($propertyName){
        $tmp = [];
        foreach($this->collection as $idx => $v){
            $value = \System\Std\Obj::getPropertyValue($v, $propertyName);
            $tmp[$value] = $v;
        }
        $this->collection = $tmp;
        unset($tmp);
        return $this;
    }
    
    public function groupConcat($field){
        $result = new \System\Collections\ArrayList();
        foreach($this->collection as $v){
            $item = \System\Std\Obj::getProperties($v);
            $result->add($item[$field]);
        }
        return $result;
    }

    /**
     * Applys a callback function to every row in the rowset.
     * 
     * @param   callable $func
     * @return  System.Data.Entity.DbListResult
     */
    public function each(callable $func){
        foreach($this->collection as $k=>$v){
            $this->collection[$k] = $func($v, $k);
        }
        return $this;
    }
    
    /**
     * Applys a callback function to every row that filters the rowset.
     * 
     * @param   callable $func
     * @return  System.Data.Entity.DbListResult
     */
    public function where(callable $func){
        $this->collection = array_filter($this->collection, $func);
        return $this;
    }
    
    /**
     * Groups the rowset using the specified $field name.
     * 
     * @param   string $field
     * @param   bool $useKey = false
     * @return  System.Data.Entity.DbListResult
     */
    public function groupBy($field, $useKey = false){
        $tmp = array();
        foreach($this->collection as $v){
            $item = \System\Std\Obj::getProperties($v);
            $tmp[$item[$field]] = $v;
        }
        $this->collection = ($useKey) ? $tmp : array_values($tmp);
        unset($tmp);
        return $this;
    }
    
    /**
     * Sorts the rowset using the specified $field name.
     * 
     * @param   string $field
     * @return  System.Data.Entity.DbListResult
     */
    public function sortBy($field){
        usort($this->collection, function($a, $b) use($field) {
            $array1 = \System\Std\Obj::getProperties($a);
            $array2 = \System\Std\Obj::getProperties($b);
            return $array1[$field] > $array2[$field];
        });
        return $this;
    }
    
    /**
     * Reverses the order of the rowset.
     * 
     * @return  System.Data.Entity.DbListResult
     */
    public function reverse(){
        $this->collection = array_reverse($this->collection);
        return $this;
    }
    
    /**
     * Limits the rowset using the specified $limit. If $offset is supplied, 
     * then limits the rowset from the start of $offset.
     * 
     * @param   int $limit
     * @param   int $offset = 0
     * @return  System.Data.Entity.DbListResult
     */
    public function limit($limit, $offset = 0){
        $this->collection = array_slice($this->collection, $offset, $limit);
        return $this;
    }
    
    /**
     * Gets the sum of the rowset for a column using the specified $field.
     * 
     * @param   string $field
     * @return  int
     */
    public function sum($field){
        $sum = 0;
        foreach($this->collection as $v){
            $item = \System\Std\Obj::getProperties($v);
            $sum += (int)$item[$field];
        }
        return $sum;
    }
    
    /**
     * Gets the average of the rowset for a column using the specified $field.
     * 
     * @param   string $field
     * @return  int
     */
    public function avg($field){
        $sum = 0;
        foreach($this->collection as $v){
            $item = \System\Std\Obj::getProperties($v);
            $sum += (int)$item[$field];
        }
        return $sum / count($this->collection);
    }
    
    /**
     * Gets the minimum value for a column using the specified $field.
     * 
     * @param   string $field
     * @return  mixed
     */
    public function min($field){
        $this->sortBy($field);

        if(isset($this->collection[0])){
            $item = \System\Std\Obj::getProperties($this->collection[0]);
            return $item[$field];
        }
    }
    
    /**
     * Gets the maximum value for a column using the specified $field.
     * 
     * @param   string $field
     * @return  mixed
     */
    public function max($field){
        $this->sortBy($field);
        $count = $this->count()-1;
        if(isset($this->collection[$count])){
            $item = \System\Std\Obj::getProperties($this->collection[$count]);
            return $item[$field];
        }
    }
    
    /**
     * Determines if the collection has items.
     * 
     * @return  bool
     */
    public function hasItems(){
        if(count($this->collection) > 0){
            return true;
        }
        return false;
    }
    
    /**
     * Determines if the collection has a row using the specified index.
     * 
     * @param   mixed $index
     * @return  bool
     */
    public function hasRow($index){
        if(array_key_exists($index, $this->collection)){
            return true;
        }
        return false;
    }
    
    public function get($index){
        if(array_key_exists($index, $this->collection)){
            return $this->collection[$index];
        }
        return false;
    }

    public function removeAt($index){
        unset($this->collection[$index]);
    }
    
    public function add($value){
        $this->collection[] = $value;
    }
    
    public function insertAt($index, $value){
        array_splice($this->collection, $index, 0, [$value]);
    }
    
    public function set($index, $value){
        $this->collection[$index] = $value;
    }
    
    /**
     * Gets the first element from the collection.
     * 
     * @return  mixed
     */
    public function first(){
        return reset($this->collection);
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
     * Gets a count of all rows in the rowset.
     * 
     * @param   string $field
     * @return  mixed
     */
    public function count(){
        return count($this->collection);
    }
    
    /**
     * Gets the result as an array.
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
}
