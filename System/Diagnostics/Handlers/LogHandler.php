<?php

namespace System\Diagnostics\Handlers;

abstract class LogHandler {
    
    protected $httpContext = null;
    protected $processor = array();
    protected $filters = array();

    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
        return $this;
    }

    /**
     * Sets a callback that can be used to process each log entry before
     * it is handled by the LogHandler.
     * 
     * @param   callable $processor
     * @return  @this
     */
    public function setProcessor($processor){
        $this->processor = $processor;
        return $this;
    }
    
    /**
     * Sets an array of level filters.
     * 
     * @param   array $filters
     * @return  @this
     */
    public function setLevelFilters(array $filters){
        $this->filters = $filters;
        return $this;
    }

    /**
     * Abstract method that must be implemented in derived classes.
     * 
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