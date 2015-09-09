<?php

namespace System\Web\UI;

class HtmlBuilder {
    
    protected $html = '';
    
    public function __call($name, $args){
        
        $attributes = array();
        
        if(isset($args[0]) && is_array($args[0])){
            $attributes = $args[0];
        }

        $this->html .= '<'.str_replace('_', '/', $name) . ' ';

        foreach($attributes as $attribute=>$value){
            $this->html .= $attribute.'="'.htmlspecialchars($value).'" ';
        }
        $this->html = trim($this->html) . '>';
        return $this;
    }
    
    public function text($text){
        $this->html .= htmlspecialchars($text);
        return $this;
    }

    public function html($html){
        $this->html .= $html;
        return $this;
    }
    
    public function __toString(){
        return $this->html;
    }
}