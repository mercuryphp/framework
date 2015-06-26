<?php

namespace System\Configuration;

abstract class ConfigurationReader {
    
    protected $config = array();
    
    public function __construct($fileName = null){
        if($fileName){
            $this->open($fileName);
        }
    }
    
    public function open($fileName){

        $data = file($fileName);

        $nodeName = '';
        
        $idx = 0;
        foreach($data as $line){
            if ($line[0] != " "){
                $nodeName = trim($line);
                $idx =0;
            }else{
                $pos = strpos($line, ':');
                
                if($pos > -1){
                    list($key, $value) = explode(':', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                }else{
                    $key = $idx;
                    $value = trim($line);
                    ++$idx;
                }
                
                if($value[0] == '[' && $value[strlen($value)-1] == ']'){
                    $innerValue = substr($value, 1, -1);
                    $value = str_getcsv($innerValue);
                }
                $this->config[$nodeName][$key] = $value;
            }
        }
        $this->init();
    }
    
    protected function getItem($key){
        if(array_key_exists($key, $this->config)){
            return new \System\Collections\Dictionary($this->config[$key]);
        }
        return false;
    }
}

?>
