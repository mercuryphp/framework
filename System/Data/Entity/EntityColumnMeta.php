<?php

namespace System\Data\Entity;

class EntityColumnMeta {
    
    protected $columnMeta;
    
    public function __construct(array $columnMeta){
        $this->columnMeta = $columnMeta;
    }
    
    public function getMeta($columnName, $property){
        if(array_key_exists($columnName, $this->columnMeta)){
            $column = $this->columnMeta[$columnName];
            
            if(array_key_exists($property, $column)){
                return $column[$property];
            }
        }
        return false;
    }
}

?>