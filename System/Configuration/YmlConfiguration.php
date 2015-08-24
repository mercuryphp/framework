<?php

namespace System\Configuration;

class YmlConfiguration extends Configuration {
    
    public function __construct($fileName = null){
        if($fileName){
            $this->open($fileName);
        }
    }
    
    /**
     * Opens a YAML like configuration file.
     * Throws ConfigurationFileNotFoundException if the file does not exist.
     * 
     * @method  open
     * @param   string $fileName
     * @return  void
     */
    public function open($fileName){
        $config = array();
        if(is_file($fileName)){
            $data = file($fileName);
            $section = array();
            $sectionName = '';

            foreach($data as $line){
                $line = trim($line);

                if(substr($line, -1) == ':'){
                    $sectionName = substr($line,0, -1);
                    $section[$sectionName] = array();
                }else if($line == ''){
                    if(count($section) > 0){
                        $this->parseSection($section);
                        $section = array();
                    }
                }else{
                    if($line){
                        $segments = explode(':', $line, 2);

                        if(count($segments) == 1){
                            $section[$sectionName][] = trim($segments[0]);
                        }

                        if(count($segments) == 2){
                            $property = trim($segments[0]);
                            $value = $segments[1];
                            $section[$sectionName][$property] = trim($value);
                        }
                    }
                }

            }
            $this->parseSection($section);
        }else{
            throw new ConfigurationFileNotFoundException('Unable to load configuration file. The file does not exist.');
        }
    }

    protected function parseSection($section){
        $config = end($section);
        $section = array_reverse($section);
        $idx = 0;
        foreach($section as $key=>$val) {
            $config = array($key => ($idx > 0) ? array_merge($config, $val) : $val);
            $idx++;
        }
        $this->config = array_merge($this->config, $config);
    }
}
