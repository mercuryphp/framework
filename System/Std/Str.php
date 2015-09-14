<?php

namespace System\Std;

final class Str{
    
    private $string = '';
    
    const FIRST_FIRST = 1;
    const FIRST_LAST = 2;
    const LAST_FIRST = 3;
    const LAST_LAST = 4;

    public function __construct($string = ''){
        $this->string = $string;
    }
    
    public function toUpper(){
        return new Str(strtoupper($this->string));
    }
    
    public function toLower(){
        return new Str(strtolower($this->string));
    }
    
    public function toUpperFirst(){
        return new Str(ucfirst($this->string));
    }
    
    public function toLowerFirst(){
        return new Str(lcfirst($this->string));
    }
    
    public function replace($search, $replace){
        return new Str(str_replace($search, $replace, $this->string));
    }
    
    public function append($string){
        return new Str($this->string.$string);
    }
    
    public function appendLine($string){
        return new Str($this->string.$string.PHP_EOL);
    }

    public function prepend($string){
        return new Str($string.$this->string);
    }

    public function trim($char = null){
        return new Str(trim($this->string, $char));
    }
    
    public function truncate($length, $append = null){

        if(strlen($this->string) > $length){
            $string = $this->subString(0,$length);
        }else{
            return new Str($this->string);
        }

        if($append){
            $string = $string->append($append);
        }
        return $string;
    }
    
    public function subString($start, $length = null){
        if(is_null($length)){
            return new Str(substr($this->string, $start));
        }
        return new Str(substr($this->string, $start, $length));
    }

    public function length(){
        return strlen($this->string);
    }

    public function split($delimiter, $limit = null, $flags = PREG_SPLIT_NO_EMPTY){
        $array = preg_split('/'.$delimiter.'/', $this->string, $limit, $flags);
        return new \System\Collections\ArrayList($array);
    }
    
    public function indexOf($char){
        return stripos($this->string, $char);
    }
    
    public function lastIndexOf($char){
        return strripos($this->string, $char);
    }

    public function get($fromChar, $toChar, $mode = Str::FIRST_FIRST){
        switch ($mode){
            case self::FIRST_FIRST:
                $pos1 = $this->indexOf($fromChar);
                $pos2 = $this->indexOf($toChar);
                break;
            case self::FIRST_LAST:
                $pos1 = $this->indexOf($fromChar);
                $pos2 = $this->lastIndexOf($toChar);
                break;
            case self::LAST_FIRST:
                $pos1 = $this->lastIndexOf($fromChar);
                $pos2 = $this->indexOf($toChar);
                break;
            case self::LAST_LAST:
                $pos1 = $this->lastIndexOf($fromChar);
                $pos2 = $this->lastIndexOf($toChar);
                break;
        }

        if($pos1 >-1 && $pos2 >-1){
            return new Str($this->subString((int)$pos1+1, (int)$pos2-$this->length()));
        }
        return new Str('');
    }
    
    public function tokenize($openingChar, $closingChar){
        $len = strlen($this->string);
        $token = '';
        $tokens = array();
        
        for($i=0; $i < $len; $i++){
            $char = $this->string[$i];

            if($char==$openingChar){
                if($token){
                    $tokens[] = $token;
                    $token = '';
                }
                continue;
            }
            if($char==$closingChar){
                $tokens[] = $openingChar.$token.$closingChar;
                $token = '';
                continue;
            }

            $token.= $char;
            
            if($i==strlen($this->string)-1){
                $tokens[] = $token;
            }
        }
        return $tokens;
    }

    public function toString(){
        return (string)$this->string;
    }
    
    public function __toString(){
        return $this->toString();
    }
    
    public static function set($string){
        return new Str($string);
    }
    
    public static function join($glue, $array, $removeEmptyEntries = true){
        $join = '';
        if(is_array($array)){
            foreach($array as $value){
                if(is_scalar($value)){
                    if($removeEmptyEntries){
                        if($value !=''){
                            $join.= $value.$glue;
                        }
                    }else{
                        $join.= $value.$glue;
                    }
                }
            }
        }
        return new Str(trim($join, $glue));
    }
}