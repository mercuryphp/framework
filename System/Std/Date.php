<?php

namespace System\Std;

class Date {
    
    protected $year;
    protected $month;
    protected $day;
    protected $hour = 0;
    protected $minute = 0;
    protected $second = 0;
    protected $format = 'c';
    protected $dayIndex = 0;
    protected $cultureInfo;

    public function __construct($year, $month, $day, $hour = null, $minute = null, $second = null){
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        
        $date = new \DateTime();
        $date->setDate($this->year, $this->month, $this->day);
        $date->setTime($this->hour, $this->minute, $this->second);
        $this->dayIndex = $date->format('N');
        
        $this->cultureInfo = \System\Std\Environment::getCulture();
    }
    
    public function getYear(){
        return $this->year;
    }
    
    public function getMonth(){
        return $this->month;
    }
    
    public function getDay(){
        return $this->day;
    }
    
    public function addDays($days){
        $date = new \DateTime();
        $date->setDate($this->year, $this->month, $this->day);
        $date->setTime($this->hour, $this->minute, $this->second);
        $date->modify('+'.$days.' day');
        return self::toDate($date);
    }
    
    public function getTimestamp(){
        $date = new \DateTime();
        $date->setDate($this->year, $this->month, $this->day);
        $date->setTime($this->hour, $this->minute, $this->second);
        return $date->getTimestamp();
    }
    
    public function toLongDateString(){
        return $this->toString($this->cultureInfo->getLongDatePattern());
    }
    
    public function toShortDateString(){
        return $this->toString($this->cultureInfo->getShortDateString());
    }
    
    public function toString($format){

        $len = strlen($format);
        $j = 0;
        $tokens = array();
        $f = array('d', 'm', 'M', 'y');
        
        for($i=0; $i<$len; $i++){
            $char = $format[$i];

            if(in_array($char, $f)){
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
                    $string .= (int)$this->day;
                    break;
                case 'dd':
                    $string .= $this->day;
                    break;
                case 'ddd':
                    $string .= $this->cultureInfo->getDayNames()->getShortName((int)$this->dayIndex);
                    break;
                case 'dddd':
                    $string .= $this->cultureInfo->getDayNames()->getFullName((int)$this->dayIndex);
                    break;
                case 'M':
                    $string .= (int)$this->month;
                    break; 
                case 'MM':
                    $string .= $this->month;
                    break;
                case 'MMM':
                    $string .= $this->cultureInfo->getMonthNames()->getShortName((int)$this->month-1);
                    break;
                case 'MMMM':
                    $string .= $this->cultureInfo->getMonthNames()->getFullName((int)$this->month-1);
                    break;
                case 'yyyy':
                    $string .= $this->year;
                    break;
                default:
                    $string .= $token;
                    break;
            }
            
        }
        
        return $string; 
    }
    
    public static function parse($string){ 
        if(strlen($string) == 8){
            $year = substr($string, 0, 4);
            $month = substr($string, 4, 2);
            $day = substr($string, 6, 2);
            return new Date($year, $month, $day, 0,0,0);
        }
        if($string){
            $date = new \DateTime($string);
            return self::toDate($date);
        }
    }

    public static function now(){
        $date = new \DateTime('NOW');
        return self::toDate($date);
    }
    
    private static function toDate($date){
        return new Date($date->format('Y'), $date->format('m'), $date->format('d'), $date->format('H'), $date->format('i'), $date->format('s'));
    } 
}

?>