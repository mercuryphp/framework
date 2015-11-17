<?php

namespace System\Data;

class QueryException extends \Exception {
    protected $sql;
    protected $params;
    
    public function __construct($message, $sql, $params) {
        parent::__construct($message);
        $this->sql = $sql;
        $this->params = $params;
    }
    
    public function getSql(){
        return $this->sql;
    }
    
    public function getParams(){
        return $this->params;
    }
}