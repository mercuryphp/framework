<?php

namespace System\Globalization;

class CultureInfo {
    
    protected $cultureName;
    protected $xml = null;
    protected $dateTimeFormat;
    protected $numberFormat;

    /**
     * Initializes an instance of CultureInfo with a culture name or a path to 
     * a culture file.
     * 
     * @param   string $name
     */
    public function __construct($name){
        $this->cultureName = $name;
        $dataFile = (string)\System\Std\Str::set(dirname(__FILE__).'/Data/'.$name.'.xml')->replace('\\', '/');
        
        if(is_file($dataFile)){
            $this->xml = simplexml_load_file($dataFile);
        }elseif(is_file($name)){
            $name = \System\Std\Str::set($name)->replace('\\', '/');
            $this->xml = simplexml_load_file($name);
            $this->cultureName = (string)$name->get('/', '.', \System\Std\Str::LAST_FIRST);
            Cultures::add($this);
        }else{
            throw new \Exception(sprintf("Culture '%s' is not supported", $name));
        }
        
        $this->dateTimeFormat = new DateTimeFormat($this->xml->datetime);
        $this->numberFormat = new NumberFormat($this->xml->numberFormat);
    }
    
    /**
     * Gets the culture name.
     * 
     * @return  string
     */
    public function getName(){
        return $this->cultureName;
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
     * Gets a DateTimeFormat object for displaying dates and times.
     * 
     * @return  System.Globalization.DateTimeFormat
     */
    public function getDateTimeFormat(){
        return $this->dateTimeFormat;
    }

    /**
     * Gets a NumberFormat object for displaying numbers and currencies.
     * 
     * @return  System.Globalization.NumberFormat
     */
    public function getNumberFormat(){
        return $this->numberFormat;
    }
    
    public function __debugInfo() {
        return [
        ];
    }
}