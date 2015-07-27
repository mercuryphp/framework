<?php

namespace System\Globalization;

class DayNames {
    
    protected $names = null;
    
    public function __construct($names){
        $this->names = $names;
    }
    
    public function getFullName($index){
        return (string)$this->names->fullName->name[$index];
    }
    
    public function getShortName($index){
        return (string)$this->names->shortName->name[$index];
    }
}