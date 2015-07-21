<?php

namespace System\Data\Entity\Attributes;

class DataType {
    
    protected $dataType;
    
    public function __construct($dataType){
        $this->dataType = $dataType;
    }
    
    public function getDataType(){
        return $this->dataType;
    }
}

?>