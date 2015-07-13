<?php

namespace System\Data\Entity\Annotations;

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