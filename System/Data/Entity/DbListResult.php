<?php

namespace System\Data\Entity;

class DbListResult implements \IteratorAggregate, \ArrayAccess {
    
    protected $collection = array();
    
    /**
     * Initializes an instance of DbListResult with the results of a database 
     * query that returns more than one row.
     * 
     * @method  __construct
     * @param   array $result
     */
    public function __construct(array $result){
        $this->collection = $result;
    }
    
    /**
     * Applys a callback function to every row in the result set.
     * 
     * @method  each
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
     * Applys a callback function to every row that filters the result set.
     * 
     * @method  where
     * @param   callable $func
     * @return  System.Data.Entity.DbListResult
     */
    public function where(callable $func){
        $this->collection = array_filter($this->collection, $func);
        return $this;
    }
    
    /**
     * Groups the result set using the specified $field name.
     * 
     * @method  groupBy
     * @param   string $field
     * @return  System.Data.Entity.DbListResult
     */
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
    
    /**
     * Sorts the result set using the specified $field name.
     * 
     * @method  sortBy
     * @param   string $field
     * @return  System.Data.Entity.DbListResult
     */
    public function sortBy($field){
        usort($this->collection, function($a, $b) use($field) {
            $array1 = \System\Std\Object::getProperties($a);
            $array2 = \System\Std\Object::getProperties($b);
            return $array1[$field] > $array2[$field];
        });
        return $this;
    }
    
    /**
     * Reverses the order of the result set.
     * 
     * @method  reverse
     * @return  System.Data.Entity.DbListResult
     */
    public function reverse(){
        $this->collection = array_reverse($this->collection);
        return $this;
    }
    
    /**
     * Limits the result set using the specified $limit. If $offset is supplied, 
     * then limits the result set from the start of $offset.
     * 
     * @method  limit
     * @param   int $limit
     * @param   int $offset = 0
     * @return  System.Data.Entity.DbListResult
     */
    public function limit($limit, $offset = 0){
        $this->collection = array_slice($this->collection, $offset, $limit);
        return $this;
    }
    
    /**
     * Gets the sum of the result set for a column using the specified $field.
     * 
     * @method  sum
     * @param   string $field
     * @return  int
     */
    public function sum($field){
        $sum = 0;
        foreach($this->collection as $v){
            $item = \System\Std\Object::getProperties($v);
            $sum += (int)$item[$field];
        }
        return $sum;
    }
    
    /**
     * Gets the average of the result set for a column using the specified $field.
     * 
     * @method  avg
     * @param   string $field
     * @return  int
     */
    public function avg($field){
        $sum = 0;
        foreach($this->collection as $v){
            $item = \System\Std\Object::getProperties($v);
            $sum += (int)$item[$field];
        }
        return $sum / count($this->collection);
    }
    
    /**
     * Gets the minimum value for a column using the specified $field.
     * 
     * @method  min
     * @param   string $field
     * @return  mixed
     */
    public function min($field){
        $this->sortBy($field);

        if(isset($this->collection[0])){
            $item = \System\Std\Object::getProperties($this->collection[0]);
            return $item[$field];
        }
    }
    
    /**
     * Gets the maximum value for a column using the specified $field.
     * 
     * @method  max
     * @param   string $field
     * @return  mixed
     */
    public function max($field){
        $this->sortBy($field);
        $count = $this->count()-1;
        if(isset($this->collection[$count])){
            $item = \System\Std\Object::getProperties($this->collection[$count]);
            return $item[$field];
        }
    }
    
    /**
     * Gets a count of all rows in the result set.
     * 
     * @method  max
     * @param   string $field
     * @return  mixed
     */
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
