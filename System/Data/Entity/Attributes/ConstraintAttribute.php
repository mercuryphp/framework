<?php

namespace System\Data\Entity\Attributes;

abstract class ConstraintAttribute {
    
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