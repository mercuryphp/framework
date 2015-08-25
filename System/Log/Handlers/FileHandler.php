<?php

namespace System\Log\Handlers;

class FileHandler {
    
    protected $path;
    protected $singleLog;
    protected $useLock;
    
    public function __construct($path, $singleLog = false, $useLock = false){
        $this->path = $path;
        $this->singleLog = $singleLog;
        $this->useLock = $useLock;
    }
    
    public function write(array $logs, array $extra = array()){
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
        
        $logFile = ($this->singleLog) ? \System\Std\Date::now()->toString('dd-mm-yyyy') : \System\Std\Date::now()->getTimestamp();

        $fp = fopen($this->path.'/'.$logFile.'.log', 'a');
       
        if ($this->useLock){
            flock($fp, LOCK_EX);
        }
        
        fwrite($fp, (string)$formatted);
        
        if ($this->useLock) {
            flock($fp, LOCK_UN);
        }
    }
}