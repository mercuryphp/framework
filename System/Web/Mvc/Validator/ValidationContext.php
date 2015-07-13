<?php

namespace System\Web\Mvc\Validator;

class ValidationContext {
    
    protected $fields = array();
    protected $errors = array();
    
    public function add($field, $value){
        if(is_object($value)){
            $value = \System\Std\Object::getPropertyValue($value, $field);
        }
        $validationStatck = new ValidationStack($value);
        $this->fields[$field] = $validationStatck;
        return $validationStatck;
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

?>