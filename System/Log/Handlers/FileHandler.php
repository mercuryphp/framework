<?php

namespace System\Log\Handlers;

class FileHandler {
    
    protected $path;
    
    public function __construct($path){
        $this->path = $path;
    }
    
    public function write(array $logs, array $extra){
        $formatted = new \System\Std\String();
        
        foreach($extra as $key=>$value){
            $formatted = $formatted->appendLine(strtoupper($key).': '.$value);
        }
        
        $formatted = $formatted->appendLine('');
        
        foreach($logs as $log){
            $formatted = $formatted->appendLine('LEVEL: '.$log['level_name'])
                ->appendLine('DATE: '.$log['time'])
                ->appendLine('PARAMS: '.  json_encode($log['params']))
                ->appendLine('MESSAGE:')
                ->appendLine($log['message'])
                ->appendLine('');
        }
        
        $fp = fopen($this->path.'/'.microtime().'.log', 'w');
        fwrite($fp, (string)$formatted);
    }
}