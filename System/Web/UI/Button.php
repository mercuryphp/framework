<?php

namespace System\Web\UI;

class Button extends Element {
   
    protected $buttonMode;
    
    public function __construct($name, $value = '', array $attributes = array(), $buttonMode = ''){
        parent::__construct();
        
        if(is_object($value)){
            $value = \System\Std\Obj::getPropertyValue($value, $name);
        }

        $attributes['value'] = $value;
        $attributes['name'] = !array_key_exists('name',$attributes) ? $name : $attributes['name'];
        $attributes['id'] = !array_key_exists('id',$attributes) ? $name : $attributes['id'];
        
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->buttonMode = $buttonMode;
    }
    
    public function render(){

        return $this->control
            ->append('<input type="')
            ->append($this->buttonMode)
            ->append('" ')
            ->append($this->renderAttributes())
            ->append('/>')
            ->toString();
    }
}