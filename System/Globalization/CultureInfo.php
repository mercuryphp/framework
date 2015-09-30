<?php

namespace System\Globalization;

class CultureInfo {
    
    protected $xml = null;
    protected $dayNames;
    protected $monthNames;
    protected $numberFormat;

    /**
     * Initializes an instance of CultureInfo with a culture name.
     * 
     * @param   string $name
     */
    public function __construct($name = ''){
        $dataFile = \System\Std\Str::set(dirname(__FILE__).'/Data/'.$name.'.xml')->replace('\\', '/');
        
        if(is_file($dataFile)){
            $this->xml = simplexml_load_file($dataFile);
        }else{
            throw new \Exception(sprintf("Culture '%s' is not supported", $name));
        }
        
        $this->dayNames = new Names($this->xml->datetime->dayNames);
        $this->monthNames = new Names($this->xml->datetime->monthNames);
        $this->numberFormat = new NumberFormat($this->xml->numberFormat);
    }
    
    /**
     * Gets the culture display name.
     * 
     * @return  string
     */
    public function getDisplayName(){
        return (string)$this->xml->displayName;
    }
    
    /**
     * Gets the short date pattern.
     * 
     * @return  string
     */
    public function getShortDateString(){
        return (string)$this->xml->datetime->shortDatePattern;
    }
    
    /**
     * Gets the long date pattern.
     * 
     * @return  string
     */
    public function getLongDatePattern(){
        return (string)$this->xml->datetime->longDatePattern;
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
    
    /**
     * Gets a NumberFormat object.
     * 
     * @return  System.Globalization.NumberFormat
     */
    public function getNumberFormat(){
        return $this->numberFormat;
    }
}