<?php

namespace System\Globalization;

class DateTimeFormat {
    
    protected $shortDatePattern;
    protected $longDatePattern;
    protected $dateSeparator;
    protected $shortTimePattern;
    protected $longTimePattern;
    protected $timeSeparator;
    protected $dayNames;
    protected $monthNames;
    
    /**
     * Initializes an instance of DateTimeFormat.
     * 
     * @param   object $formats
     */
    public function __construct($formats){

        $this->shortDatePattern = (string)$formats->shortDatePattern;
        $this->longDatePattern = (string)$formats->longDatePattern;
        $this->dateSeparator = (string)$formats->dateSeparator;
        $this->shortTimePattern = (string)$formats->shortTimePattern;
        $this->longTimePattern = (string)$formats->longTimePattern;
        $this->timeSeparator = (string)$formats->timeSeparator;
        $this->dayNames = new Names($formats->dayNames);
        $this->monthNames = new Names($formats->monthNames);
    }
    
    /**
     * Gets the short date pattern.
     * 
     * @return  string
     */
    public function getShortDatePattern(){
        return $this->shortDatePattern;
    }
    
    /**
     * Gets the long date pattern.
     * 
     * @return  string
     */
    public function getLongDatePattern(){
        return $this->longDatePattern;
    }
    
    /**
     * Gets the date separator.
     * 
     * @return  string
     */
    public function getDateSeparator(){
        return $this->dateSeparator;
    }
    
    /**
     * Gets the short time pattern.
     * 
     * @return  string
     */
    public function getShortTimePattern(){
        return $this->shortTimePattern;
    }
    
    /**
     * Gets the time separator.
     * 
     * @return  string
     */
    public function getLongTimePattern(){
        return $this->longTimePattern;
    }
    
    /**
     * Gets the date separator.
     * 
     * @return  string
     */
    public function getTimeSeparator(){
        return $this->timeSeparator;
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
