<?php

namespace System\Data\Entity\Annotations;

class Required extends ValidationAttribute {

    public function __construct($errorMessage = ''){
        $this->errorMessage = $errorMessage;
    }
    
    public function getLength(){
        return $this->length;
    }
    
    public function isValid(){
        if($this->value == ''){
            return false;
        }
        return true;
    }
    
    public function getErrorMessage(){
        if($this->errorMessage){
            return $this->errorMessage;
        }
        return sprintf('%s is required', $this->columnName);
    }

}

?>