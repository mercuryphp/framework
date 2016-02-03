<?php

namespace System\Globalization;

class Names {
    
    protected $fullNames = array();
    protected $shortNames = array();
    
    /**
     * Initializes an instance of Names.
     * 
     * @param   object $names
     */
    public function __construct($names){
        $this->fullNames = (array)$names->fullName->name;
        $this->shortNames = (array)$names->shortName->name;
    }
    
    /**
     * Gets the full name using the specified $index.
     * 
     * @param   int $index
     * @return  mixed
     */
    public function getFullName($index){
        if(isset($this->fullNames[$index])){
            return $this->fullNames[$index];
        }
        return false;
    }
    
    /**
     * Gets the short name using the specified $index.
     * 
     * @param   int $index
     * @return  mixed
     */
    public function getShortName($index){
        if(isset($this->shortNames[$index])){
            return $this->shortNames[$index];
        }
        return false;
    }
}