<?php

namespace System\Web\UI;

abstract class Element {
    
    protected $control;
    protected $attributes = array();
    protected $attributeString;
    
    public function __construct(){
        $this->control = new \System\Std\String('');
        $this->attributeString = new \System\Std\String('');
    }
    
    protected function renderAttributes(){

        foreach($this->attributes as $attribute=>$value){
            if($value){
                $this->attributeString = $this->attributeString->append($attribute)->append('="')->append($value)->append('" ');
            }
        }
       
        return $this->attributeString->trim()->toString();
    }
    
    public abstract function render();

}

?>