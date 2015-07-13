<?php

namespace System\Web\UI;

class Link extends Element {
   
    protected $title;
    
    public function __construct($title, $href, array $attributes = array(), $params = null){
        parent::__construct();

        $attributes['href'] = $href;
        
        $this->title = $title;
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->params = $params;
    }
    
    public function render(){
        if(is_object($this->params)){
            $this->params = \System\Std\Object::getProperties($this->params);
        }

        if(is_array($this->params)){
            $href = $this->attributes['href'];
            foreach($this->params as $param=>$val){
                $href = \System\Std\String::set($href)->replace('@'.$param, $val);
            }
            $this->attributes['href'] = $href;
        }
        
        return $this->control
            ->append('<a ')
            ->append($this->renderAttributes())
            ->append('>')
            ->append($this->title)
            ->append('</a>')
            ->toString();
    }
}

?>