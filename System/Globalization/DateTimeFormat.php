<?php

namespace System\Globalization;

class DateTimeFormat {
    
    protected $formats;
    protected $dayNames;
    protected $monthNames;
    
    public function __construct($formats){
        $this->formats = $formats;
        $this->dayNames = new Names($formats->dayNames);
        $this->monthNames = new Names($formats->monthNames);
    }
    
    /**
     * Gets the short date pattern.
     * 
     * @return  string
     */
    public function getShortDateString(){
        return (string)$this->formats->shortDatePattern;
    }
    
    /**
     * Gets the long date pattern.
     * 
     * @return  string
     */
    public function getLongDatePattern(){
        return (string)$this->formats->longDatePattern;
    }
    
    /**
     * Gets a Names object that contains a collection of day names.
     * 
     * @return  System.Globalization.Names
     */
    public function getDayNames(){
        return $this->dayNames;
    }
    
    /**
     * Gets a Names object that contains a collection of month names.
     * 
     * @return  System.Globalization.Names
     */
    public function getMonthNames(){
        return $this->monthNames;
    }
}
