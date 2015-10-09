<?php

namespace System\Web\Routing;

class RouteConstraint {
    
    protected $constraints;

    const Numeric = '/^[0-9]*$/';
    const Alphabetic = '/^[a-zA-Z]*$/';
    const AlphaNumeric = '/^[a-zA-Z0-9]*$/';
    
    public function __construct(array $constraints = array()){
        $this->constraints = $constraints;
    }

    public function execute($routeValue){
        foreach($this->constraints as $name => $constraintValue){
            switch ($name) {
                case 'numeric':
                    if(!preg_match(RouteConstraint::Numeric, $routeValue)){
                        return false;
                    }
                    break;
                case 'alphabetic':
                    if(!preg_match(RouteConstraint::Alphabetic, $routeValue)){
                        return false;
                    }
                    break;
                case 'alphanumeric':
                    if(!preg_match(RouteConstraint::AlphaNumeric, $routeValue)){
                        return false;
                    }
                    break;
                case 'len':
                    if(strlen($routeValue) != $constraintValue){
                        return false;
                    }
                    break;
                case 'minlen':
                    if(strlen($routeValue) < $constraintValue){
                        return false;
                    }
                    break;
                case 'maxlen':
                    if(strlen($routeValue) > $constraintValue){
                        return false;
                    }
                    break;
                case 'contains':
                    if(is_array($constraintValue) && !in_array($routeValue, $constraintValue)){
                        return false;
                    }
                case 'date':
                    $pattern = ($constraintValue=='long') ? '/[0-9]{4}[0-9]{2}-[0-9]{2}/' : '/[0-9]{8}/';
                    if(preg_match($pattern, $routeValue)){ 
                        if($constraintValue=='long'){
                            list($year,$month,$day) = explode('-', $routeValue);
                        }else{
                            $year = substr($routeValue, 0,4); 
                            $month = substr($routeValue, 4,2);
                            $day = substr($routeValue, 6,2);
                        }
                        return checkdate($month, $day, $year);
                    }else{
                        return false;
                    }
                    break;
            }
        }
        return true;
    }
}
