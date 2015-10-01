<?php

namespace System\Data;

class Profiler {
    
    protected $logs = array();
    protected $onLogAdded = null;
    protected $lastLogId = 0;
    
    const CONNECT = 1;
    const SELECT = 2;
    const INSERT = 3;
    const UPDATE = 4;
    const DELETE = 5;

    /**
     * Logs the start time of an SQL query.
     * 
     * @return  void
     */
    public function start(){
        ++$this->lastLogId;
        $this->logs['LOG_'.$this->lastLogId]['start_time'] = microtime(true);
    }

    /**
     * Logs an SQL query and its parameters. The start() method must be called each 
     * time this method is called. If a callback has been supplied to 
     * the onLogAdded() method, it will be executed after the log has been added. 
     * The callback can be used to process individual logs.
     * 
     * @param   string $log
     * @param   array $params = array()
     * @param   mixed $type = null
     * @return  void
     */
    public function log($log, array $params = array(), $type = null){
        $id = 'LOG_'.$this->lastLogId;
        
        if(isset($this->logs[$id])){
            $array = $this->logs[$id];
            $endTime = microtime(true);

            $this->logs[$id] = array(
                'log' => $log,
                'start_time' => $array['start_time'],
                'end_time' => $endTime,
                'duration' => $endTime - $array['start_time'],
                'params' => $params
            );

            if(null === $type){
                switch (strtolower(substr(ltrim($log), 0, 6))) {
                    case 'insert':
                        $type = self::INSERT;
                        break;
                    case 'update':
                        $type = self::UPDATE;
                        break;
                    case 'delete':
                        $type = self::DELETE;
                        break;
                    case 'select':
                        $type = self::SELECT;
                        break;
                }
            }
            $this->logs[$id]['type'] = $type;
        }
        
        if(null !== $this->onLogAdded){
            call_user_func_array($this->onLogAdded, $this->logs[$id]);
        }
    }
    
    /**
     * Gets a log entry using the specified $logId.
     * 
     * @param   string $logId
     * @return  array
     */
    public function get($logId){
        if(isset($this->logs[$logId])){
            return $this->logs[$logId];
        }
    }
    
    /**
     * Gets a count of all log entries.
     * 
     * @return  int
     */
    public function count(){
        return count($this->logs);
    }
    
    /**
     * Gets the last log entry.
     * 
     * @return  array
     */
    public function last(){
        return end($this->logs);
    }
    
    /**
     * Gets the total duration of all log entries.
     * 
     * @return  int
     */
    public function getTotalDuration(){
        $duration = 0;
        foreach($this->logs as $log){
            $duration +=$log['duration'];
        }
        return $duration;
    }
    
    /**
     * Sets a user defined callback function that is executed when a 
     * log has been added.
     * 
     * @param   callable $func
     * @return  void
     */
    public function onLogAdded(callable $func){
        $this->onLogAdded = $func;
    }

    /**
     * Gets a filtered result using the specified $conditions.
     * 
     * @param   array $conditions
     * @return  System.Collections.Dictionary
     */
    public function where(array $conditions){
        return new \System\Collections\Dictionary(array_filter($this->logs, function ($log) use($conditions){
            foreach($conditions as $key => $value){
                if(array_key_exists($key, $log) && $log[$key] == $value){
                    return $log;
                }
            }
        }));
    }

    /**
     * Gets the underlying log array.
     * 
     * @return  array
     */
    public function toArray(){
        return $this->logs;
    }
}