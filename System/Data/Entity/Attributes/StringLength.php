<?php

namespace System\Data\Entity\Attributes;

class StringLength extends ConstraintAttribute {
    
    protected $length;
    
    /**
     * Initializes an instance of StringLength with a length value and
     * an optional error message.
     * 
     * @param   string $errorMessage
     */
    public function __construct($length, $errorMessage = ''){
        $this->length = (int)$length;
        $this->errorMessage = $errorMessage;
    }
    
    /**
     * Gets the length value.
     * 
     * @return  int
     */
    public function getLength(){
        return $this->length;
    }
    
    /**
     * Gets a boolean value that determines if validation was successful.
     * 
     * @return  bool
     */
    public function isValid(){
        if(is_string($this->value) && strlen($this->value) > $this->length){
            return false;
        }
        return true;
    }
    
    /**
     * Gets the validation error message. If a user defined error message has not
     * been set, then gets a default error message.
     * 
     * @return  string
     */
    public function getMessage(){
        if($this->errorMessage){
            return $this->errorMessage;
        }
        return sprintf('%s must be less than %s characters', $this->columnName, $this->length);
    }

}