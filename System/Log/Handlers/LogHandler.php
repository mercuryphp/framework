<?php

namespace System\Log\Handlers;

abstract class LogHandler {
    
    protected $processor = array();
    protected $filters = array();
    
    public function setProcessor($processor){
        $this->processor = $processor;
        return $this;
    }
    
    public function setFilters(array $filters){
        $this->filters = $filters;
        return $this;
    }

    public abstract function write(array $logs, array $extra = array());
    
    protected function executeProcessor($log){
        if($this->processor){
            $processor = $this->processor;
            return $processor($log, $this);
        }
        return $log;
    }
}