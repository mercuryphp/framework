<?php

namespace System\Web\UI;

class FileUpload extends Element {

    public function __construct($name, $attributes = array()){
        parent::__construct();
        
        $attributes['name'] = !array_key_exists('name',$attributes) ? $name : $attributes['name'];
        $attributes['id'] = !array_key_exists('id',$attributes) ? $name : $attributes['id'];
        
        $this->attributes = array_merge($this->attributes, $attributes);
    }
    
    public function render(){

        return $this->control
            ->append('<input type="file" ')
            ->append($this->renderAttributes())
            ->append('/>')
            ->toString();
    }
}

?>