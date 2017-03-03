<?php

namespace System\Data;

class QueryException extends \PDOException {
    
    protected $sql;
    protected $params;
    protected $state;
    
    public function __construct($message, $sql, $params, $code) {
        parent::__construct($message);
        $this->sql = $sql;
        $this->params = $params;
        $this->code = $code;
    }

    public function getSql(){
        return $this->sql;
    }
    
    public function getParams(){
        return $this->params;
    }
}