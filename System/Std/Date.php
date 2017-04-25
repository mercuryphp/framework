<?php

namespace System\Std;

class Date extends \DateTime {
    
    protected $cultureInfo;

    /**
     * Initializes a new instance of the Date class.
     * 
     * @param   string $string
     * @param   string $timeZone = null
     */
    public function __construct($string, $timeZone = null){
        
        if($timeZone){
            $timeZone = new \DateTimeZone($timeZone);
        }else{
            if(Environment::getTimezone()){
                $timeZone = new \DateTimeZone(Environment::getTimezone());
            }
        }
        
        try{
            parent::__construct($string, $timeZone);
        }catch(\Exception $e){
            throw new \Exception(sprintf('Date::__construct(): Failed to parse datetime string (%s).', $string));
        }
        $this->cultureInfo = Environment::getCulture();
    }
    
    /**
     * Gets the year component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getYear(){
        return (int)$this->format('Y');
    }
    
    /**
     * Gets the month component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getMonth(){
        return $this->format('m');
    }
    
    /**
     * Gets the day component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getDay(){
        return $this->format('d');
    }
    
    /**
     * Gets the hour component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getHour(){
        return (int)$this->format('H');
    }
    
    /**
     * Gets the minute component of the date represented by this instance.
     * 
     * @return  string
     */
    public function getMinute(){
        return $this->format('i');
    }
    
     /**
     * Gets the second component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getSecond(){
        return (int)$this->format('s');
    }
    
     /**
     * Gets the day index component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getDayIndex(){
        return (int)$this->format('N');
    }
    
     /**
     * Gets the week index component of the date represented by this instance.
     * 
     * @return  int
     */
    public function getWeekIndex(){
        return (int)$this->format('W');
    }

    /**
     * Returns a new System.Std.Date that adds the value of $days to the 
     * value of this instance.
     * 
     * @param   int $days
     * @return  System.Std.Date
     */
    public function addDays($days){
        $this->modify('+'.$days.' day');
        return $this;
    }
    
    public static function getDateRange($date, $days, $format = 'yyyy-MM-d'){
        $dates = [];
        for($d = 0; $d < $days; $d++){
            $dates[] = $date->modify('1 day')->toString($format);
        }
        return $dates;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $months to the 
     * value of this instance.
     * 
     * @param   int $months
     * @return  System.Std.Date
     */
    public function addMonths($months){
        $this->modify('+'.$months.' month');
        return $this;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $years to the 
     * value of this instance.
     * 
     * @param   int $years
     * @return  System.Std.Date
     */
    public function addYears($years){
        $this->modify('+'.$years.' year');
        return $this;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $hours to the 
     * value of this instance.
     * 
     * @param   int $hours
     * @return  System.Std.Date
     */
    public function addHours($hours){
        $this->modify('+'.$hours.' hour');
        return $this;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $minutes to the 
     * value of this instance.
     * 
     * @param   int $minutes
     * @return  System.Std.Date
     */
    public function addMinutes($minutes){
        $this->modify('+'.$minutes.' minute');
        return $this;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $seconds to the 
     * value of this instance.
     * 
     * @param   int $seconds
     * @return  System.Std.Date
     */
    public function addSeconds($seconds){
        $this->modify('+'.$seconds.' second');
        return $this;
    }

    /**
     * Gets a formatted date string based on the current cultures longDatePattern.
     * 
     * @return  string
     */
    public function toLongDateString(){
        return $this->toString($this->cultureInfo->getDateTimeFormat()->getLongDatePattern());
    }
    
    /**
     * Gets a formatted date string based on the current cultures shortDatePattern.
     * 
     * @return  string
     */
    public function toShortDateString(){
        return $this->toString($this->cultureInfo->getDateTimeFormat()->getShortDatePattern());
    }
    
    /**
     * Gets a formatted time string based on the current cultures longTimePattern.
     * 
     * @return  string
     */
    public function toLongTimeString(){
        return $this->toString($this->cultureInfo->getDateTimeFormat()->getLongTimePattern());
    }
    
    /**
     * Gets a formatted time string based on the current cultures shortTimePattern.
     * 
     * @return  string
     */
    public function toShortTimeString(){
        return $this->toString($this->cultureInfo->getDateTimeFormat()->getShortTimePattern());
    }
    
    /**
     * Sets a System.Globalization.CultureInfo object for this instance.
     * 
     * @param   System.Globalization.CultureInfo $cultureInfo
     * @return  System.Std.Date
     */
    public function setCulture(\System\Globalization\CultureInfo $cultureInfo){ 
        $this->cultureInfo = $cultureInfo;
        return $this;
    }
    
    /**
     * Gets a formatted date string specified by $format. If $format is not specified,
     * then the default format from the environment setting is used.
     * 
     * @param   string $format
     * @return  string
     */
    public function toString($format = null){

        if($format==null){
            $format = Environment::getDateTimeFormat();
        }

        $len = strlen($format);
        $j = 0;
        $tokens = array();
        $array = array('d','M','y','H','m', 's');
        
        for($i=0; $i<$len; $i++){
            $char = $format[$i];

            if(in_array($char, $array)){
                if(isset($tokens[$j])){
                    $tokens[$j] .= $char;
                }else{
                    $tokens[$j] = $char;
                } 
            }else{
                $j++;
                $tokens[$j] = $char;
                $j++;
            }
        }

        $string = '';
        foreach($tokens as $token){
            $format = trim($token);
            
            switch ($format) {
                case 'd':
                    $string .= (int)$this->getDay();
                    break;
                case 'dd':
                    $string .= $this->getDay();
                    break;
                case 'ddd':
                    $string .= $this->cultureInfo->getDateTimeFormat()->getDayNames()->getShortName((int)$this->getDayIndex());
                    break;
                case 'dddd':
                    $string .= $this->cultureInfo->getDateTimeFormat()->getDayNames()->getFullName((int)$this->getDayIndex());
                    break;
                case 'M':
                    $string .= (int)$this->getMonth();
                    break; 
                case 'MM':
                    $string .= $this->getMonth();
                    break;
                case 'MMM':
                    $string .= $this->cultureInfo->getDateTimeFormat()->getMonthNames()->getShortName((int)$this->getMonth()-1);
                    break;
                case 'MMMM':
                    $string .= $this->cultureInfo->getDateTimeFormat()->getMonthNames()->getFullName((int)$this->getMonth()-1);
                    break;
                case 'yyyy':
                    $string .= $this->getYear();
                    break;
                case 'HH':
                    $string .= $this->getHour();
                    break;
                case 'mm':
                    $string .= $this->getMinute();
                    break;
                case 's':
                    $string .= $this->getSecond();
                    break;
                case 'ss':
                    $string .= $this->getSecond() < 10 ? '0'.$this->getSecond() : $this->getSecond();
                    break;
                default:
                    $string .= $token;
                    break;
            }
        }
        return $string; 
    }
    
    public function __toString(){
        return $this->toString();
    }
    
    /**
     * Converts a date string to a System.Std.Date
     * 
     * @param   string $string
     * @param   string $timeZone = null
     * @return  System.Std.Date
     */
    public static function parse($string, $timeZone = null){ 
        if($string){
            return new Date($string, $timeZone);
        }
    }

    /**
     * Gets a Date object that is set to the current date and time.
     * 
     * @param   string $timeZone = null
     * @return  System.Std.Date
     */
    public static function now($timeZone = null){
        return new Date('NOW', $timeZone);
    }
}