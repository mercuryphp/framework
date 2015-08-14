<?php

namespace System\Log;

class Logger {
    
    protected $logs = array();
    protected $extra = array();
    
    const DEBUG = 1;
    const INFO = 2;
    const NOTICE = 3;
    const WARNING = 4;
    const ERROR = 5;
    const EXCEPTION = 6;
    
    protected static $levels = array(
        1 => 'DEBUG',
        2 => 'INFO',
        3 => 'NOTICE',
        4 => 'WARNING',
        5 => 'ERROR',
        6 => 'EXCEPTION',
    );
    
    protected $handlers = array();
    
    public function debug($message, array $params = array()){
        $this->addLog(static::DEBUG, $message, $params);
        return $this;
    }
    
    public function info($message, array $params = array()){
        $this->addLog(static::INFO, $message, $params);
        return $this;
    }
    
    public function notice($message, array $params = array()){
        $this->addLog(static::NOTICE, $message, $params);
        return $this;
    }
    
    public function warning($message, array $params = array()){
        $this->addLog(static::WARNING, $message, $params);
        return $this;
    }
    
    public function error($message, array $params = array()){
        $this->addLog(static::ERROR, $message, $params);
        return $this;
    }
    
    public function critical($message, array $params = array()){
        $this->addLog(static::CRITICAL, $message, $params);
        return $this;
    }
    
    public function exception(\Exception $e, array $params = array()){
        $this->addLog(static::EXCEPTION, $e, $params);
        return $this;
    }

    public function addHandler($handler){
        $this->handlers[] = $handler;
    }
    
    public function flush(){
        foreach($this->handlers as $handler){
            $handler->write($this->logs, $this->extra);
        }
    }
    
    public function add($key, $value){ 
        $this->extra[$key] = $value; 
    }
    
    protected function addLog($level, $message, $params){
        $this->logs[] = array(
            'message' => $message, 
            'time' => \System\Std\Date::now()->toString(),
            'level' => $level,
            'level_name' => static::$levels[$level],
            'params' => $params,
        );
    }
}