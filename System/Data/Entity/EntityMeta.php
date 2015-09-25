<?php

namespace System\Data\Entity;

class EntityMeta {

    protected $entityName;
    protected $meta;
    
    /**
     * Initializes an instance of EntityMeta that encapsulates meta data for
     * an entity type.
     * 
     * @param   string $entityName
     * @param   array $meta
     */
    public function __construct($entityName, array $meta){
        $this->entityName = $entityName;
        $this->meta = $meta;    
    }
    
    /**
     * Gets the entity type name.
     * 
     * @return  string
     */
    public function getEntityName(){
        return $this->entityName;
    }
    
    /**
     * Gets a System.Data.Entity.Attributes.Table object that contains the table
     * name the entity is mapped to.
     * 
     * @return  System.Data.Entity.Attributes.Table
     */
    public function getTable(){
        return $this->meta['System.Data.Entity.Attributes.Table'];
    }
    
    /**
     * Gets a System.Data.Entity.Attributes.Key object that contains the unique
     * key name the entity is mapped to.
     * 
     * @return  System.Data.Entity.Attributes.Key
     */
    public function getKey(){
        return $this->meta['System.Data.Entity.Attributes.Key'];
    }
    
    /**
     * Gets an array of column meta data.
     * 
     * @return  array
     */
    public function getColumns(){
        return $this->meta['Columns'];
    }
    
    /**
     * Gets an array of attributes for the specified column.
     * 
     * @param   string $columnName
     * @return  array
     */
    public function getColumnAttributes($columnName){
        if(array_key_exists($columnName, $this->meta['Columns'])){
            return $this->meta['Columns'][$columnName];
        }
    }
    
    /**
     * Gets an array of validator attributes. If $fieldName is specified, then
     * gets an array of validator attributes for the specified $fieldName.
     * 
     * @param   string $fieldName
     * @return  array
     */
    public function getValidators($fieldName = ''){
        $tmp = array();
        
        foreach($this->meta['Columns'] as $columnName => $attributes){
            foreach($attributes as $attribute){
                if($attribute instanceof \System\Web\Mvc\Validators\IValidator){
                    $tmp[$columnName][] = $attribute;
                }
            }
            if($fieldName && ($columnName == $fieldName)){
                return $tmp[$columnName];
            }
        }
        return $tmp;
    }
}