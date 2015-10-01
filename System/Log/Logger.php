<?php

namespace System\Log;

class Logger {
    
    protected $handlers = array();
    protected $processor;
    protected $logs = array();
    protected $extra = array();
    protected static $levels = array(
        1 => 'DEBUG',
        2 => 'INFO',
        3 => 'NOTICE',
        4 => 'WARNING',
        5 => 'ERROR',
        6 => 'EXCEPTION',
    );
    
    const DEBUG = 1;
    const INFO = 2;
    const NOTICE = 3;
    const WARNING = 4;
    const ERROR = 5;
    const EXCEPTION = 6;

    /**
     * Initializes an instance of Logger with an optional LogHandler.
     * 
     * @param   System.Log.Handlers.LogHandler $handler = null
     */
    public function __construct($handler = null){
        if($handler){
            $this->addHandler($handler);
        }
    }

    /**
     * Adds a message to the logger and sets the log level to debug. 
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function debug($message, array $params = array()){
        $this->addLog(static::DEBUG, $message, $params);
        return $this;
    }
    
    /**
     * Adds a message to the logger and sets the log level to info. 
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function info($message, array $params = array()){
        $this->addLog(static::INFO, $message, $params);
        return $this;
    }
    
    /**
     * Adds a message to the logger and sets the log level to notice. 
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function notice($message, array $params = array()){
        $this->addLog(static::NOTICE, $message, $params);
        return $this;
    }
    
    /**
     * Adds a message to the logger and sets the log level to warning.  
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function warning($message, array $params = array()){
        $this->addLog(static::WARNING, $message, $params);
        return $this;
    }
    
    /**
     * Adds a message to the logger and sets the log level to error. 
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function error($message, array $params = array()){
        $this->addLog(static::ERROR, $message, $params);
        return $this;
    }
    
    /**
     * Adds a message to the logger and sets the log level to critical. 
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function critical($message, array $params = array()){
        $this->addLog(static::CRITICAL, $message, $params);
        return $this;
    }
    
    /**
     * Adds a message to the logger and sets the log level to exception. 
     * 
     * @param   string $message
     * @param   array $params
     * @return  System.Log.Logger
     */
    public function exception($message, array $params = array()){
        $this->addLog(static::EXCEPTION, $message, $params);
        return $this;
    }
    
    /**
     * Adds a log handler. 
     * 
     * @param   System.Log.Handlers.LogHandler $handler
     * @param   array $filters
     * @return  System.Log.Logger
     */
    public function addHandler(\System\Log\Handlers\LogHandler $handler, array $filters = array()){
        $this->handlers[] = array('handler' => $handler, 'filters' => $filters);
        return $this;
    }
    
    /**
     * Sets a callback function that can be used to process each log entry before
     * it is handled by the LogHandler.
     * 
     * @param   callable $processor
     * @return  System.Log.Logger
     */
    public function setProcessor(callable $processor){
        $this->processor = $processor;
        return $this;
    }
    
    /**
     * Executes all log handlers and terminates the script.
     * 
     * @return  void
     */
    public function flush(){
        foreach($this->handlers as $handler){
            $handler['handler']
                ->setProcessor($this->processor)
                ->setLevelFilters($handler['filters'])
                ->write($this->logs, $this->extra);
        }
        exit;
    }
    
    /**
     * Adds extra information to the log.
     * 
     * @param   string $key
     * @param   string $value
     * @return  System.Log.Logger
     */
    public function add($key, $value){ 
        $this->extra[$key] = $value; 
        return $this;
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