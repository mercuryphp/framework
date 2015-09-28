<?php

namespace System\Data\Entity\Attributes;

abstract class ConstraintAttribute implements \System\Web\Mvc\Validators\IValidator {
    
    protected $columnName;
    protected $value;
    protected $errorMessage;

    /**
     * Sets the column name for an entity property that requires validation.
     * 
     * @param   string $columnName
     */
    public function setColumnName($columnName){
        $this->columnName = $columnName;
    }
    
    /**
     * Sets the value for an entity property that requires validation.
     * 
     * @param   string $value
     */
    public function setValue($value){
        $this->value = $value;
    }
    
    /**
     * An abstract method that must be implemented to return an error message
     * when validation fails.
     * 
     * @return  string
     */
    public abstract function getMessage();
}