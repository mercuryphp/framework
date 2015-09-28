<?php

namespace System\Data\Entity\Attributes;

class DataType {
    
    protected $dataType;
    
    /**
     * Initializes an instance of DataType with a schema data type for an entity
     * property.
     * 
     * @param   string $dataType
     */
    public function __construct($dataType){
        $this->dataType = $dataType;
    }
    
    /**
     * Gets the schema data type.
     * 
     * @return  string
     */
    public function getDataType(){
        return $this->dataType;
    }
}