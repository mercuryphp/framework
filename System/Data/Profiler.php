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

    public function start(){
        ++$this->lastLogId;
        $this->logs['LOG_'.$this->lastLogId]['start_time'] = microtime(true);
    }

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
    
    public function get($logId){
        if(isset($this->logs[$logId])){
            return $this->logs[$logId];
        }
    }
    
    public function count(){
        return count($this->logs);
    }
    
    public function last(){
        return end($this->logs);
    }
    
    public function getTotalDuration(){
        $duration = 0;
        foreach($this->logs as $log){
            $duration +=$log['duration'];
        }
        return $duration;
    }
    
    public function onLogAdded(callable $callback){
        $this->onLogAdded = $callback;
    }

    public function toArray(){
        return $this->logs;
    }
}
