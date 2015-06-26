<?php

namespace System\Configuration;

class NamespaceSection {

    protected $namespaces = array();
    
    public function __construct($section){
        if($section instanceof \System\Collections\Dictionary){
            $array = $section->toArray();
            foreach($array as $item){
                $segments = explode('.', $item);
                $name = array_pop($segments);
                $this->namespaces[$name] = $item;
            }
        }
    }
    
    public function toArray(){
        return $this->namespaces;
    }
}

?>