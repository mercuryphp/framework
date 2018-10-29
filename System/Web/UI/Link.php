<?php

namespace System\Web\UI;

class Link extends Element {
   
    protected $title;
    protected $params = array();
    
    public function __construct($title, $href, array $attributes = array(), $params = null){
        parent::__construct();

        $attributes['href'] = $href;
        
        $this->title = $title;
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->params = $params;
    }
    
    public function setParams($params){
        $this->params = $params;
    }
    
    public function render(){
        if(is_object($this->params)){
            $this->params = \System\Std\Obj::getProperties($this->params);
        }

        if(is_array($this->params)){
            $href = $this->attributes['href'];
            foreach($this->params as $param=>$val){
                if(is_scalar($val)){
                    $href = \System\Std\Str::set($href)->replace('@'.$param, $val);
                }
            }
            $this->attributes['href'] = (string)$href;
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