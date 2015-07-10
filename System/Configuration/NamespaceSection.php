<?php

namespace System\Configuration;

class NamespaceSection  extends \System\Collections\Dictionary {

    public function __construct($section){
        $namespaces = array();
        foreach($section as $item){
            $segments = explode('.', $item);
            $name = array_pop($segments);
            $namespaces[$name] = $item;
        }
        
        parent::__construct($namespaces);
    }
}

?>