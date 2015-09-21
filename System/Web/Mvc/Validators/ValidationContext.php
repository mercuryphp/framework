<?php

namespace System\Web\Mvc\Validators;

class ValidationContext {
    
    protected $fields = array();
    protected $errors = array();
    
    public function add($field, $value){
        if(is_object($value)){
            $value = \System\Std\Object::getPropertyValue($value, $field);
        }
        if(array_key_exists($field, $this->fields)){
            $validationStack = $this->fields[$field];
        }else{
            $validationStack = new ValidationStack($value);
            $this->fields[$field] = $validationStack;
        }
        return $validationStack;
    }
    
    public function addRange($field, $value, array $validators){
        foreach($validators as $validator){
            $validator->setColumnName($field);
            $this->add($field, $value)->add($validator);
        }
    }
    
    public function addError($fieldName, $errMessage){
        $this->errors[$fieldName] = $errMessage;
    }
    
    public function isValid(){
        foreach($this->fields as $fieldName => $stack){
            if(!$stack->isValid()){
                $this->errors[$fieldName] = $stack->getError();
            }
        }

        if(count($this->errors) > 0){
            return false;
        }
        return true;
    }

    public function getErrors(){
        return $this->errors;
    }
}