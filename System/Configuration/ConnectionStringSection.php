<?php

namespace System\Configuration;

class ConnectionStringSection {

    protected $connectionStrings;
    
    public function __construct($section){
        $this->connectionStrings = $section;
    }
    
    public function get($key){
        return $this->connectionStrings->get($key);
    }
}

?>