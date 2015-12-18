<?php

namespace System\Data;

class QueryException extends \PDOException {
    
    protected $sql;
    protected $params;
    protected $state;
    
    public function __construct($message, $sql, $params) {
        parent::__construct($message);
        $this->sql = $sql;
        $this->params = $params;
        
        if(substr($message,0,8) == 'SQLSTATE'){
            $this->state = (int)\System\Std\Str::set($message)->get('[', ']', \System\Std\Str::FIRST_FIRST)->toString();
        }
    }
    
    public function getState(){
        return $this->state;
    }

    public function getSql(){
        return $this->sql;
    }
    
    public function getParams(){
        return $this->params;
    }
}