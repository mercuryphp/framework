<?php

namespace System\Configuration\Readers;

class YamlReader extends Reader {
    
    public function __construct($fileName = null){
        if($fileName){
            $this->open($fileName);
        }
    }
    
    public function open($fileName){
        if(is_file($fileName)){
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

                    if($value && ($value[0] == '[' && $value[strlen($value)-1] == ']')){
                        $innerValue = substr($value, 1, -1);
                        $value = str_getcsv($innerValue);
                    }
                    $this->config[$nodeName][$key] = $value;
                }
            }
        }else{
            throw new ConfigurationFileNotFoundException('Unable to load configuration file. The file does not exist.');
        }
    }
    
    public function getItem($key){
        if(array_key_exists($key, $this->config)){
            return $this->config[$key];
        }
        return array();
    }
}

?>