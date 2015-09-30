<?php

namespace System\Globalization;

class Names {
    
    protected $names = null;
    
    /**
     * Initializes an instance of Names.
     * 
     * @param   object $names
     */
    public function __construct($names){
        $this->names = $names;
    }
    
    /**
     * Gets the full name using the specified $index.
     * 
     * @param   int $index
     * @return  string
     */
    public function getFullName($index){
        return (string)$this->names->fullName->name[$index];
    }
    
    /**
     * Gets the short name using the specified $index.
     * 
     * @param   int $index
     * @return  string
     */
    public function getShortName($index){
        return (string)$this->names->shortName->name[$index];
    }
}