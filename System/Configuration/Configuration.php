<?php

namespace System\Configuration;

abstract class Configuration {
    
    protected $config = array();
    
    /**
     * Abstract method to open a configuration file for processing.
     * 
     * @param   string $fileName
     * @return  void
     */
    public abstract function open($fileName);
    
    /**
     * Gets a configuration element using a configuration path. If no element is
     * found and $default is specified, then gets $default. 
     * 
     * @param   string $path
     * @param   mixed $default = null
     * @return  mixed
     */
    public function get($path, $default = null){
        $properties = explode('.', $path);

        $tmp = $this->config;

        foreach($properties as $property){
            if(array_key_exists($property, $tmp)){
                $tmp = $tmp[$property];
            }else{ 
                if(!is_null($default)){
                    return $default;
                }
                return false;
            }
        }
        return $tmp;
    }
}