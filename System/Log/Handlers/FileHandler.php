<?php

namespace System\Log\Handlers;

class FileHandler extends LogHandler {
    
    protected $path;
    protected $singleLog;
    protected $useLock;
    
    /**
     * Initializes an instance of FileHandler.
     * 
     * @method  __construct
     * @param   string $path
     * @param   bool $singleLog = false
     * @param   bool $useLock = false
     */
    public function __construct($path, $singleLog = false, $useLock = false){
        $this->path = $path;
        $this->singleLog = $singleLog;
        $this->useLock = $useLock;
    }
    
    /**
     * Writes all logs to a file.
     * 
     * @method  write
     * @param   array $logs
     * @param   array $extra
     * @return  void
     */
    public function write(array $logs, array $extra = array()){
        $formatted = (new \System\Std\String())->appendLine('');
        
        foreach($extra as $key=>$value){
            $formatted = $formatted->appendLine(strtoupper($key).': '.$value);
        }

        $save = false;
        foreach($logs as $log){
            if(count($this->filters) == 0 || (in_array(strtoupper($log['level_name']), array_map('strtoupper',$this->filters)))){

                $log = $this->executeProcessor($log);
                
                if($log){
                    $formatted = $formatted->appendLine('LEVEL: '.$log['level_name'])
                        ->appendLine('DATE: '.$log['time'])
                        ->appendLine('PARAMS: '.  json_encode($log['params']))
                        ->appendLine('MESSAGE:')
                        ->appendLine($log['message'])
                        ->appendLine('');
                    $save = true;
                }
            }
        }
        
        if(!$save){
            return;
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