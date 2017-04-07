<?php

namespace System\Configuration;

class JsonConfiguration extends Configuration {
    
    /**
     * Initializes an instance of JsonConfiguration.
     * Reads configuration data if $fileName is supplied.
     * 
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
                
                if(array_key_exists('import', $data)){
                    $files = $data['import'];

                    foreach($files as $file){
                        $config = new JsonConfiguration($file);
                        $this->config = array_merge($this->config, $config->toArray());
                    }
                }
            }
        }
    }
}