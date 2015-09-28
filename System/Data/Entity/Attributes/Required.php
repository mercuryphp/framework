<?php

namespace System\Data\Entity\Attributes;

class Required extends ConstraintAttribute {

    /**
     * Initializes an instance of Requied.
     * 
     * @param   string $errorMessage
     */
    public function __construct($errorMessage = ''){
        $this->errorMessage = $errorMessage;
    }
    
    /**
     * Gets a boolean value that determines if validation failed.
     * 
     * @return  bool
     */
    public function isValid(){
        if($this->value == ''){
            return false;
        }
        return true;
    }
    
    /**
     * Gets the validation error message. If a user defined error message has not
     * been set, then gets a default error message.
     * 
     * @return  bool
     */
    public function getMessage(){
        if($this->errorMessage){
            return $this->errorMessage;
        }
        return sprintf('%s is required', $this->columnName);
    }
}