<?php

namespace System\Web\Mvc\Validators;

class IsDate extends Validator {

    protected $format;

    public function __construct($errMessage, $format = 'yyyy-mm-dd'){
        $this->errMessage = $errMessage;
        $this->format = $format;
    }
    
    public function isValid(){
        $year = 0;
        $month = 0;
        $day = 0;
        $dayName = '';
        $monthName = '';

        $len = strlen($this->format);
        
        if($len != strlen($this->value)){
            return false;
        }

        for($i=0; $i < $len; $i++){
            
            $chr = $this->format[$i];

            switch ($chr) {
                case 'j':
                    $dayName .= $this->value[$i];
                    break;
                case 'f':
                    $monthName .= $this->value[$i];
                    break;
                case 'y':
                    $year .= $this->value[$i];
                    break;
                case 'm':
                    $month .= $this->value[$i];
                    break;
                case 'd':
                    $day .= $this->value[$i];
                    break;
            }
        }

        if(checkdate($month, $day, $year)){
            return true;
        }
        return false;
    }
}