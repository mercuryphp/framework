<?php

namespace System\Data\Entity\Attributes;

class Contains extends ConstraintAttribute {
    
    protected $list = array();

    public function __construct(array $list, $errorMessage = ''){
        $this->list = $list;
        $this->errorMessage = $errorMessage;
    }
    
    public function getLength(){
        return $this->length;
    }
    
    public function isValid(){
        if(!in_array($this->value, $this->list)){
            return false;
        }
        return true;
    }
    
    public function getMessage(){
        if($this->errorMessage){
            return $this->errorMessage;
        }
        return sprintf('%s does not contain the specified values %s', $this->columnName, json_encode($this->list));
    }
}