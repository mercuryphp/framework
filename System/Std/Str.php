<?php

namespace System\Std;

final class Str{
    
    private $string = '';
    
    const FIRST_FIRST = 1;
    const FIRST_LAST = 2;
    const LAST_FIRST = 3;
    const LAST_LAST = 4;

    /**
     * Initializes an instance of Str with an optional string.
     * 
     * @param   string $string = ''
     */
    public function __construct($string = ''){
        $this->string = $string;
    }
    
    /**
     * Gets a new Str instance where all characters are converted to upper case.
     * 
     * @return  System.Std.Str
     */
    public function toUpper(){
        return new Str(strtoupper($this->string));
    }
    
    /**
     * Gets a new Str instance where all characters are converted to lower case.
     * 
     * @return  System.Std.Str
     */
    public function toLower(){
        return new Str(strtolower($this->string));
    }
    
    /**
     * Gets a new Str instance where the first character in the string is
     * converted to upper case.
     * 
     * @return  System.Std.Str
     */
    public function toUpperFirst(){
        return new Str(ucfirst($this->string));
    }
    
    /**
     * Gets a new Str instance where the first character in the string is
     * converted to lower case.
     * 
     * @return  System.Std.Str
     */
    public function toLowerFirst(){
        return new Str(lcfirst($this->string));
    }
    
    /**
     * Gets a new Str instance where all occurrences of a specified string in this 
     * instance is replaced with another specified string.
     * 
     * @param   string $search
     * @param   string $replace
     * @return  System.Std.Str
     */
    public function replace($search, $replace){
        return new Str(str_replace($search, $replace, $this->string));
    }
    
    /**
     * Gets a new Str instance with the specified string appened to this 
     * instance.
     * 
     * @param   string $string
     * @return  System.Std.Str
     */
    public function append($string){
        return new Str($this->string.$string);
    }
    
    /**
     * Gets a new Str instance with the specified string and an end of line 
     * character appened to this instance.
     * 
     * @param   string $string
     * @return  System.Std.Str
     */
    public function appendLine($string){
        return new Str($this->string.$string.PHP_EOL);
    }

    /**
     * Gets a new Str instance with the specified string prepended to this instance.
     * 
     * @param   string $string
     * @return  System.Std.Str
     */
    public function prepend($string){
        return new Str($string.$this->string);
    }

    /**
     * Gets a new Str instance where all occurrences of $char are removed from 
     * this instance.
     * 
     * @param   string $charList = null
     * @return  System.Std.Str
     */
    public function trim($charList = null){
        return new Str(trim($this->string, $charList));
    }
    
    /**
     * Gets a new Str instance where all occurrences of $char are removed from 
     * the start of this instance.
     * 
     * @param   string $charList = null
     * @return  System.Std.Str
     */
    public function leftTrim($charList = null){
        return new Str(ltrim($this->string, $charList));
    }
    
    /**
     * Gets a new Str instance where all occurrences of $char are removed from 
     * the end of this instance.
     * 
     * @param   string $charList = null
     * @return  System.Std.Str
     */
    public function rightTrim($charList = null){
        return new Str(rtrim($this->string, $charList));
    }
    
    /**
     * Gets a new Str instance where this instance is truncated to a specified 
     * length with an optional string appened to the instance.
     * 
     * @param   int $length
     * @param   string $appendString = null
     * @return  System.Std.Str
     */
    public function truncate($length, $appendString = null){

        if(strlen($this->string) > $length){
            $string = $this->subString(0,$length);
        }else{
            return $this;
        }

        if(!is_null($appendString)){
            $string = $string->append($appendString);
        }
        return $string;
    }
    
    /**
     * Gets a new Str instance which is a sub string of this instance.
     * 
     * @param   string $start
     * @param   int $length = null
     * @return  System.Std.Str
     */
    public function subString($start, $length = null){
        if(is_null($length)){
            return new Str(substr($this->string, $start));
        }
        return new Str(substr($this->string, $start, $length));
    }

    /**
     * Gets the number of characters in the current instance.
     * 
     * @return  int
     */
    public function length(){
        return strlen($this->string);
    }

    /**
     * Splits a string into substrings using the $delimiter and returns a 
     * System.Collections.ArrayList containing the substrings.
     * 
     * @param   string $delimiter
     * @param   int $limit = null
     * @param   int $flags
     * @return  System.Collections.ArrayList
     */
    public function split($delimiter, $limit = null, $flags = PREG_SPLIT_NO_EMPTY){
        $array = preg_split('/'.$delimiter.'/', $this->string, $limit, $flags);
        return new \System\Collections\ArrayList($array);
    }
    
    /**
     * Gets the zero-based index of the first occurrence of the specified $char
     * in the current instance.
     * 
     * @param   string $string
     * @return  int
     */
    public function indexOf($string){
        return stripos($this->string, $string);
    }
    
    /**
     * Gets the zero-based index of the last occurrence of the specified $char
     * in the current instance.
     * 
     * @param   string $string
     * @return  int
     */
    public function lastIndexOf($string){
        return strripos($this->string, $string);
    }

    /**
     * Gets a new Str instance which is a substring of this instance using a 
     * $fromChar and a $toChar.
     * 
     * @param   string $fromChar
     * @param   string $toChar
     * @param   string $mode
     * @return  System.Std.Str
     */
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
    
    /**
     * Tokenizes the current instance and returns an instance of 
     * System.Collections.ArrayList that contains all the tokens.
     * 
     * @param   string $openingChar
     * @param   string $closingChar
     * @return  System.Collections.ArrayList
     */
    public function tokenize($openingChar, $closingChar){
        $len = strlen($this->string);
        $token = '';
        $tokens = new \System\Collections\ArrayList();
        
        for($i=0; $i < $len; $i++){
            $char = $this->string[$i];

            if($char==$openingChar){
                if($token){
                    $tokens->add($token);
                    $token = '';
                }
                continue;
            }
            if($char==$closingChar){
                $tokens->add($openingChar.$token.$closingChar);
                $token = '';
                continue;
            }

            $token.= $char;
            
            if($i==strlen($this->string)-1){
                $tokens->add($token);
            }
        }
        return $tokens;
    }

    /**
     * Gets the string for this instance.
     * 
     * @param   string $format = null
     * @param   System.Globalization.CultureInfo $cultureInfo = null
     * @return  string
     */
    public function toString($format = null, \System\Globalization\CultureInfo $cultureInfo = null){
        if(!is_null($format)){
            if(is_null($cultureInfo)){
                $cultureInfo = \System\Std\Environment::getCulture();
            }
            
            $arg = null;
            if(stripos($format, ':') > -1){
                list($format, $arg) = explode(':', $format, 2);
            }

            switch($format){
                case 'C':
                    return $cultureInfo->getNumberFormat()->formatCurrency($this->string);
                case 'N':
                    return $cultureInfo->getNumberFormat()->formatNumber($this->string);
                case 'X':
                    return dechex((float)$this->string);
                case 'R':
                    return round((float)$this->string, $arg);
                case 'd':
                    return Date::parse($this->string)->setCulture($cultureInfo)->toShortDateString();
                case 'D':
                    return Date::parse($this->string)->setCulture($cultureInfo)->toLongDateString();
                case 't':
                    return Date::parse($this->string)->setCulture($cultureInfo)->toShortTimeString();
                case 'T':
                    return Date::parse($this->string)->setCulture($cultureInfo)->toLongTimeString();
            }
        }
        return (string)$this->string;
    }
    
    /**
     * Gets the string for this instance by calling the toString() method.
     * 
     * @param   string $format = null
     * @param   System.Globalization.CultureInfo $cultureInfo = null
     * @return  string
     */
    public function __toString($format = null, \System\Globalization\CultureInfo $cultureInfo = null){
        return $this->toString($format, $cultureInfo);
    }
    
    /**
     * Sets the string and gets a new instance of Str.
     * 
     * @param   string $string
     * @return  System.Std.Str
     */
    public static function set($string){
        return new Str($string);
    }
    
    /**
     * Joins all elements in the array using the specified $glue and returns a 
     * new instance of System.Std.Str
     * 
     * @param   string $glue
     * @param   array $array
     * @param   bool $removeEmptyEntries = true
     * @return  System.Std.Str
     */
    public static function join($glue, array $array, $removeEmptyEntries = true){
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