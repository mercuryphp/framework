<?php

namespace System\Web\Mvc\Validators;

abstract class Validator {
    
    protected $fieldName;
    protected $value;
    protected $errMessage;
    
    /**
     * Sets the field name.
     * 
     * @param   string $fieldName
     * @return  void
     */
    public function setFieldName($fieldName){
        $this->fieldName = $fieldName;
    }
    
    public function setValue($value){
        $this->value = $value;
    }
    
    public abstract function isValid();
    
    public function getMessage(){
        return $this->errMessage;
    }
}