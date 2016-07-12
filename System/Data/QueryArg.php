<?php

namespace System\Data;

class QueryArg {
    
    protected $sql;
    protected $params = array();

    public function __construct($sql, $params = array()){
        $this->sql = $sql;
        $this->params = $params;
    }
    
    public function setSql($sql){
        $this->sql = $sql;
    }
    
    public function getSql(){
        return $this->sql;
    }
    
    public function setParams($params){
        $this->params = $params;
    }
    
    public function getParams(){
        return $this->params;
    }
}
