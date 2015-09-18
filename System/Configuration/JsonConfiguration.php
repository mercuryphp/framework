<?php

namespace System\Configuration;

class JsonConfiguration extends Configuration {
    
    /**
     * Initializes an instance of JsonConfiguration.
     * Reads configuration data if $fileName is supplied.
     * 
     * @method  __construct
     * @param   string $fileName = null
     */
    public function __construct($fileName = null){
        if($fileName){
            $this->open($fileName);
        }
    }
    
    /**
     * Opens a JSON configuration file.
     * 
     * @method  open
     * @param   string $fileName
     * @return  void
     */
    public function open($fileName){

        if(is_file($fileName)){
            $data = file_get_contents($fileName);
            
            if(substr($data, 0, 5) == '<?php'){
                $sections = explode('?>', $data);
                $data = trim($sections[1]);
            }
            $data = json_decode($data, true);
            
            if(is_array($data)){
                $this->config = $data;
            }
        }
    }
}