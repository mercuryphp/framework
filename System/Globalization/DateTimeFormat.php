<?php

namespace System\Globalization;

class DateTimeFormat {
    
    protected $formats;
    protected $dayNames;
    protected $monthNames;
    
    /**
     * Initializes an instance of DateTimeFormat.
     * 
     * @param   object $formats
     */
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
    public function getShortDatePattern(){
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
     * Gets the date separator.
     * 
     * @return  string
     */
    public function getDateSeparator(){
        return (string)$this->formats->dateSeparator;
    }
    
    /**
     * Gets the short time pattern.
     * 
     * @return  string
     */
    public function getShortTimePattern(){
        return (string)$this->formats->shortTimePattern;
    }
    
    /**
     * Gets the time separator.
     * 
     * @return  string
     */
    public function getLongTimePattern(){
        return (string)$this->formats->longTimePattern;
    }
    
    /**
     * Gets the date separator.
     * 
     * @return  string
     */
    public function getTimeSeparator(){
        return (string)$this->formats->timeSeparator;
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
