<?php

namespace System\Data\Entity\Attributes;

class Contains extends ConstraintAttribute {
    
    protected $list = array();

    /**
     * Initializes an instance of Contains with an array list and an error message.
     * 
     * 
     * @param   array $list
     * @param   string $errorMessage
     */
    public function __construct(array $list, $errorMessage = ''){
        $this->list = $list;
        $this->errorMessage = $errorMessage;
    }
    
    /**
     * Gets a boolean value that determines if validation failed.
     * 
     * @return  bool
     */
    public function isValid(){
        if(!in_array($this->value, $this->list)){
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
        return sprintf('%s does not contain the specified values %s', $this->columnName, json_encode($this->list));
    }
}