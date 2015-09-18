<?php

namespace System\Std;

class Date extends \DateTime {
    
    protected $cultureInfo;

    /**
     * Initializes a new instance of the Date class.
     * 
     * @method  __construct
     * @param   string $string
     * @param   string $timeZone = null
     * @return  void
     */
    public function __construct($string, $timeZone = null){
        
        if($timeZone){
            $timeZone = new \DateTimeZone($timeZone);
        }else{
            if(Environment::getTimezone()){
                $timeZone = new \DateTimeZone(Environment::getTimezone());
            }
        }
        
        parent::__construct($string, $timeZone);
        $this->cultureInfo = Environment::getCulture();
    }
    
    /**
     * Gets the year component of the date represented by this instance.
     * 
     * @method  getYear
     * @return  int
     */
    public function getYear(){
        return (int)$this->format('Y');
    }
    
    /**
     * Gets the month component of the date represented by this instance.
     * 
     * @method  getYear
     * @return  int
     */
    public function getMonth(){
        return $this->format('m');
    }
    
    /**
     * Gets the day component of the date represented by this instance.
     * 
     * @method  getDay
     * @return  int
     */
    public function getDay(){
        return $this->format('d');
    }
    
    /**
     * Gets the hour component of the date represented by this instance.
     * 
     * @method  getHour
     * @return  int
     */
    public function getHour(){
        return (int)$this->format('H');
    }
    
    /**
     * Gets the minute component of the date represented by this instance.
     * 
     * @method  getMinute
     * @return  string
     */
    public function getMinute(){
        return $this->format('i');
    }
    
     /**
     * Gets the second component of the date represented by this instance.
     * 
     * @method  getSecond
     * @return  int
     */
    public function getSecond(){
        return (int)$this->format('s');
    }
    
     /**
     * Gets the day index component of the date represented by this instance.
     * 
     * @method  getDayIndex
     * @return  int
     */
    public function getDayIndex(){
        return (int)$this->format('N');
    }
    
     /**
     * Gets the week index component of the date represented by this instance.
     * 
     * @method  getWeekIndex
     * @return  int
     */
    public function getWeekIndex(){
        return (int)$this->format('W');
    }

    /**
     * Returns a new System.Std.Date that adds the value of $days to the 
     * value of this instance.
     * 
     * @method  addDays
     * @param   int $days
     * @return  System.Std.Date
     */
    public function addDays($days){
        $this->modify('+'.$days.' day');
        return $this;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $hours to the 
     * value of this instance.
     * 
     * @method  addHours
     * @param   int $hours
     * @return  \System\Std\Date
     */
    public function addHours($hours){
        $this->modify('+'.$hours.' hour');
        return $this;
    }
    
    /**
     * Returns a new System.Std.Date that adds the value of $minutes to the 
     * value of this instance.
     * 
     * @method  addMinutes
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
     * @method  addSeconds
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
     * @method  toLongDateString
     * @return  string
     */
    public function toLongDateString(){
        return $this->toString($this->cultureInfo->getLongDatePattern());
    }
    
    /**
     * Gets a formatted date string based on the current cultures shortDatePattern.
     * 
     * @method  toShortDateString
     * @return  string
     */
    public function toShortDateString(){
        return $this->toString($this->cultureInfo->getShortDateString());
    }
    
    /**
     * Gets a formatted date string specified by $format. If $format is not specified,
     * then the default format from the environment setting is used.
     * 
     * @method  toString
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
                    $string .= $this->cultureInfo->getDayNames()->getShortName((int)$this->getDayIndex());
                    break;
                case 'dddd':
                    $string .= $this->cultureInfo->getDayNames()->getFullName((int)$this->getDayIndex());
                    break;
                case 'M':
                    $string .= (int)$this->getMonth();
                    break; 
                case 'MM':
                    $string .= $this->getMonth();
                    break;
                case 'MMM':
                    $string .= $this->cultureInfo->getMonthNames()->getShortName((int)$this->getMonth()-1);
                    break;
                case 'MMMM':
                    $string .= $this->cultureInfo->getMonthNames()->getFullName((int)$this->getMonth()-1);
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
                case 'ss':
                    $string .= $this->getSecond();
                    break;
                default:
                    $string .= $token;
                    break;
            }
        }
        return $string; 
    }
    
    /**
     * Converts a date string to a System.Std.Date
     * 
     * @method  parse
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
     * @method  now
     * @param   string $timeZone = null
     * @return  System.Std.Date
     */
    public static function now($timeZone = null){
        return new Date('NOW', $timeZone);
    }
}