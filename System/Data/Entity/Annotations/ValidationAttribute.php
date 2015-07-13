<?php

namespace System\Data\Entity\Annotations;

abstract class ValidationAttribute {
    
    protected $columnName;
    protected $value;
    protected $errorMessage;

    public function setColumnName($columnName){
        $this->columnName = $columnName;
    }
    
    public function setValue($value){
        $this->value = $value;
    }

    public abstract function isValid();
    
    public abstract function getErrorMessage();
}

?>