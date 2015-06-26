<?php

namespace System\Configuration;

class AppSettingSection {

    protected $settings;
    
    public function __construct($section){
        $this->settings = $section;
    }
    
    public function get($key){
        return $this->settings->get($key);
    }
}

?>