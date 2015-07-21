<?php

namespace System\Data\Entity\Attributes;

class Table {
    
    protected $tableName;
    
    public function __construct($tableName){
        $this->tableName = $tableName;
    }
    
    public function getTableName(){
        return $this->tableName;
    }
}

?>