<?php

namespace System\Configuration;

abstract class Configuration {
    
    protected $config = array();
    
    public abstract function open($fileName);
    
    /**
     * Gets an element from the configuration using a specified $key.
     * 
     * @method  get
     * @param   string $path
     * @return  mixed
     */
    public function get($path, $default = ''){
        $properties = explode('.', $path);

        $tmp = $this->config;

        foreach($properties as $property){
            if(array_key_exists($property, $tmp)){
                $tmp = $tmp[$property];
            }else{ 
                if($default ){
                    return $default;
                }
                return false;
            }
        }
        return $tmp;
    }
}
