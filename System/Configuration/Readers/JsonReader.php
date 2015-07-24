<?php

namespace System\Configuration\Readers;

class JsonReader extends Reader {
    
    public function __construct($fileName = null){
        if($fileName){
            $this->open($fileName);
        }
    }
    
    public function open($fileName){

        if(is_file($fileName)){
            $data = file_get_contents($fileName);
            
            if(substr($data, 0, 5) == '<?php'){
                $sections = explode('?>', $data);
                $data = trim($sections[1]);
            }
            $this->config = json_decode($data, true);

            if(!is_array($this->config)){
                throw new ConfigurationException('Unable to load configuration file. The file maybe corrupt.');
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
