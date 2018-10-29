<?php

namespace System\Web\UI;

class TextBox extends Element {
   
    protected $textMode;
    
    public function __construct($name, $value = '', array $attributes = array(), $textMode = 'text'){
        parent::__construct();
        
        if(is_object($value)){
            $value = \System\Std\Obj::getPropertyValue($value, $name);
        }

        $attributes['value'] = $value;
        $attributes['name'] = !array_key_exists('name',$attributes) ? $name : $attributes['name'];
        $attributes['id'] = !array_key_exists('id',$attributes) ? str_replace(['.','[',']',], '_', $name) : str_replace(['.','[',']'], '_', $attributes['id']);
        
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->textMode = $textMode;
    }
    
    public function render(){

        if($this->textMode =='textarea'){
            $value = $this->attributes['value'];
            unset($this->attributes['value']);
            return $this->control
                ->append('<textarea ')
                ->append($this->renderAttributes())
                ->append('>')
                ->append($value)
                ->append('</textarea>')    
                ->toString();
        }
        
        return $this->control
            ->append('<input type="')
            ->append($this->textMode)
            ->append('" ')
            ->append($this->renderAttributes())
            ->append('/>')
            ->toString();
    }
}