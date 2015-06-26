<?php

namespace System\Web\UI;

class TextBox extends Element {
   
    protected $source;
    
    public function __construct($name, $value = '', array $attributes = array()){
        parent::__construct();
        
        $attributes['value'] = $value;
        $attributes['name'] = $name;
        $attributes['id'] = $name;
        
        $this->name = $name;
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    public function render(){

        return $this->control
            ->append('<input type="text" ')
            ->append($this->renderAttributes())
            ->append('>')
            ->append('</select>')
            ->toString();
    }
}

?>