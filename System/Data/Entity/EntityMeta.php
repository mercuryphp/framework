<?php

namespace System\Data\Entity;

class EntityMeta {
    
    protected $entityName;
    protected $table;
    protected $key;
    protected $columns = array();
    
    public function setEntityName($entityName){
        $this->entityName = $entityName;
    }
    
    public function getEntityName(){
        return $this->entityName;
    }
    
    public function setTable($tableName){
        $this->table = $tableName;
    }
    
    public function getTable(){
        return $this->table;
    }
    
    public function setKey($key){
        $this->key = $key;
    }
    
    public function getKey(){
        return $this->key;
    }
    
    public function setColumns($columns){
        $this->columns = $columns;
    }
    
    public function getColumns(){
        return $this->columns;
    }
}

?>