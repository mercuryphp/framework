<?php

namespace System\Data;

class Transaction {
    
    protected $pdo;
    
    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function beginTransaction(){
        return $this->pdo->beginTransaction();
    }
    
    public function commit(){
        return $this->pdo->commit();
    }
    
    public function rollBack(){
        return $this->pdo->rollBack();
    }
    
    public function inTransaction(){
        return $this->pdo->inTransaction();
    }
}

?>