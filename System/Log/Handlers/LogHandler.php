<?php

namespace System\Log\Handlers;

abstract class LogHandler {
    
    protected $processor = array();
    protected $filters = array();
    
    /**
     * Sets a callback that can be used to process each log entry before
     * it is handled by the LogHandler.
     * 
     * @method  setProcessor
     * @param   callable $processor
     * @return  System.Log.Handlers.LogHandler
     */
    public function setProcessor($processor){
        $this->processor = $processor;
        return $this;
    }
    
    /**
     * Sets an array of level filters.
     * 
     * @method  setFilters
     * @param   array $filters
     * @return  System.Log.Handlers.LogHandler
     */
    public function setLevelFilters(array $filters){
        $this->filters = $filters;
        return $this;
    }

    /**
     * Abstract method that must be implemented in derived classes.
     * 
     * @method  write
     * @param   array $logs
     * @param   array $extra
     * @return  void
     */
    public abstract function write(array $logs, array $extra = array());
    
    protected function executeProcessor($log){
        if($this->processor){
            $processor = $this->processor;
            return $processor($log, $this);
        }
        return $log;
    }
}