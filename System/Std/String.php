<?php

namespace System\Std;

final class String{
    
    private $string = '';

    public function __construct($string){
        $this->string = $string;
    }
    
    public function toUpper(){
        return new String(strtoupper($this->string));
    }
    
    public function toLower(){
        return new String(strtolower($this->string));
    }
    
    public function toUpperFirst(){
        return new String(ucfirst($this->string));
    }
    
    public function toLowerFirst(){
        return new String(lcfirst($this->string));
    }
    
    public function replace($search, $replace){
        return new String(str_replace($search, $replace, $this->string));
    }
    
    public function append($string){
        return new String($this->string.$string);
    }
    
    public function prepend($string){
        return new String($string.$this->string);
    }
    
    public function trim($char = null){
        return new String(trim($this->string, $char));
    }
    
    public function truncate($length, $append = null){

        if(strlen($this->string) > $length){
            $string = $this->subString(0,$length);
        }else{
            return new String($this->string);
        }

        if($append){
            $string = $string->append($append);
        }
        return $string;
    }
    
    public function subString($start, $length){
        return new String(substr($this->string, $start, $length));
    }

    public function length(){
        return strlen($this->string);
    }

    public function split($delimiter, $limit = null){
        if($limit){
            $array = explode($delimiter, $this->string, $limit);
        }else{
            $array = explode($delimiter, $this->string);
        }
        return new \System\Collections\ArrayList($array);
    }
    
    public function lineBreak(){
        return new String(nl2br($this->string));
    }
    
    public function toString(){
        return $this->string;
    }
    
    public function __toString(){
        return $this->toString();
    }
    
    public static function set($string){
        return new String($string);
    }
    
    public static function join($glue, $array, $removeEmptyEntries = true){
        $join = '';
        if(is_array($array)){
            foreach($array as $value){
                if($removeEmptyEntries){
                    if((string)$value){
                        $join.= $value.$glue;
                    }
                }else{
                    $join.= $value.$glue;
                }
            }
        }
        return new String(trim($join, $glue));
    }
}

?>
