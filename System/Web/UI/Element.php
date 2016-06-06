<?php

namespace System\Web\UI;

abstract class Element {
    
    protected $control;
    protected $attributes = array();
    protected $attributeString;
    protected $escaper = null;
    
    public function __construct(){
        $this->control = new \System\Std\Str('');
        $this->attributeString = new \System\Std\Str('');
    }
    
    public function setEscaper(callable $callback){
        $this->escaper = $callback;
    }
    
    public function getEscaper(){
        return $this->escaper;
    }
    
    public function setAttribute($name, $value){
        $this->attributes[$name] = $value;
    }
    
    public function getAttribute($name){
        if(array_key_exists($name, $this->attributes)){
            return $this->attributes[$name];
        }
    }

    public abstract function render();
    
    public function __toString(){
        return $this->render();
    }
    
    protected function renderAttributes(){
        foreach($this->attributes as $attribute=>$value){
            if(is_string($value)){
                $this->attributeString = $this->attributeString->append($attribute)->append('="')->append($this->escape($value))->append('" ');
            }
        }
        return $this->attributeString->trim()->toString();
    }
    
    protected function escape($value){
        if(null === $this->escaper){
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        }else{
            return call_user_func_array($this->escaper, array($value));
        }
    }
}