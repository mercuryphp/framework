<?php

namespace System\Web\UI;

class Label extends Element {
   
    protected $text;
    
    public function __construct($text, array $attributes = array()){
        parent::__construct();
        $this->text = $text;
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    public function render(){
        return $this->control
            ->append('<label')
            ->append($this->renderAttributes())
            ->append('>')
            ->append($this->text)
            ->append('</label>')
            ->toString();
    }
}