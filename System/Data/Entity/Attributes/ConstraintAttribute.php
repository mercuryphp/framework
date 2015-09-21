<?php

namespace System\Data\Entity\Attributes;

abstract class ConstraintAttribute implements \System\Web\Mvc\Validators\IValidator {
    
    protected $columnName;
    protected $value;
    protected $errorMessage;

    public function setColumnName($columnName){
        $this->columnName = $columnName;
    }
    
    public function setValue($value){
        $this->value = $value;
    }
    public abstract function getMessage();
}