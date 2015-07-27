<?php

namespace System\Data\Entity;

class EntityMeta {

    protected $entityName;
    protected $meta;
    
    public function __construct($entityName, $meta){
        $this->entityName = $entityName;
        $this->meta = $meta;    
    }
    
    public function getEntityName(){
        return $this->entityName;
    }
    
    public function getTable(){
        return $this->meta['System.Data.Entity.Attributes.Table'];
    }
    
    public function getKey(){
        return $this->meta['System.Data.Entity.Attributes.Key'];
    }
    
    public function getColumns(){
        return $this->meta['Columns'];
    }
    
    public function getColumnAttributes($columnName){
        if(array_key_exists($columnName, $this->meta['Columns'])){
            return $this->meta['Columns'][$columnName];
        }
    }
}

?>