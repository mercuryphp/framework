<?php

namespace System\Data\Entity\Attributes;

class Table {
    
    protected $tableName;
    
    /**
     * Initializes an instance of Table with a value that indicates the table
     * name for the entity.
     * 
     * @param   string $tableName
     */
    public function __construct($tableName){
        $this->tableName = $tableName;
    }
    
    /**
     * Initializes an instance of Table with a value that indicates the table
     * name for the entity.
     * 
     * @return  string
     */
    public function getTableName(){
        return $this->tableName;
    }
}