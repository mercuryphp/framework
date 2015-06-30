<?php

namespace System\Std;

class Date extends \DateTime {
    
    protected $cultureInfo;

    public function __construct($string, $timezone){
        
        if(Environment::getTimezone()){
            $timezone = new \DateTimeZone(Environment::getTimezone());
        }
        
        parent::__construct($string, $timezone);
        
        $this->cultureInfo = Environment::getCulture();
    }
    
    public function getYear(){
        return $this->format('Y');
    }
    
    public function getMonth(){
        return $this->format('m');
    }
    
    public function getDay(){
        return $this->format('d');
    }
    
    public function getHour(){
        return $this->format('H');
    }
    
    public function getMinute(){
        return $this->format('i');
    }
    
    public function getSecond(){
        return $this->format('s');
    }
    
    public function getDayIndex(){
        return $this->format('N');
    }
    
    public function getWeekIndex(){
        return $this->format('W');
    }
    
    public function addDays($days){
        $this->modify('+'.$days.' day');
        return $this;
    }
    
    public function addHours($hours){
        $this->modify('+'.$hours.' hour');
        return $this;
    }
    
    public function addMinutes($minutes){
        $this->modify('+'.$minutes.' minute');
        return $this;
    }
    
    public function addSeconds($seconds){
        $this->modify('+'.$seconds.' second');
        return $this;
    }

    public function toLongDateString(){
        return $this->toString($this->cultureInfo->getLongDatePattern());
    }
    
    public function toShortDateString(){
        return $this->toString($this->cultureInfo->getShortDateString());
    }
    
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
    
    public static function parse($string, $timeZone = null){ 
        if($string){
            return new Date($string, $timeZone);
        }
    }

    public static function now($timeZone = null){
        return new Date('NOW', $timeZone);
    }
}

?>