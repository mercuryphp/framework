<?php

namespace System\Web\UI;

class Hidden extends TextBox {
   
    public function __construct($name, $value = '', array $attributes = array()){
        parent::__construct($name, $value);
        
        if(is_object($value)){
            $value = \System\Std\Object::getPropertyValue($value, $name);
        }
        
        $attributes['value'] = $value;
        $attributes['name'] = !array_key_exists('name',$attributes) ? $name : $attributes['name'];
        $attributes['id'] = !array_key_exists('id',$attributes) ? $name : $attributes['id'];
        
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    public function render(){
        return $this->control
            ->append('<input type="hidden" ')
            ->append($this->renderAttributes())
            ->append('/>')
            ->toString();
    }
}

?>