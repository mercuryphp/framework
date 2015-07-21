<?php

namespace System\Data;

class Connection {
    
    protected $pdo;
    protected $transaction;
    
    public function __construct($connectionString){
        $dbConfig = new \System\Collections\Dictionary();
        
        if(strpos($connectionString, ';')){
            $params = explode(';', $connectionString);
            foreach($params as $param){
                if($param && strpos($param, '=')){
                    list($name, $value) = explode('=', $param, 2);
                    $dbConfig[$name] = $value;
                }
            }
        }
        
        $uid = $dbConfig['uid'];
        $pwd = $dbConfig['pwd'];
        
        unset($dbConfig['uid']);
        unset($dbConfig['pwd']);

        try{
            $this->pdo = new \PDO(
                $dbConfig->each(function($k, $v){ return $k.'='.$v.';'; })->join(''),
                $uid,
                $pwd
            );

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->transaction = new Transaction($this->pdo);
        }catch(\PDOException $e){
            throw new ConnectionException($e->getMessage());
        }
    }
    
    public function query($sql, array $params = array()){
        if (!$this->pdo) { return false; }

        try{
            $stm = $this->pdo->prepare($sql);
            $stm->execute($params);
        }catch (\PDOException $e){
            throw new QueryException($e->getMessage());
        }
        return $stm;
    }
	
    public function fetch($sql, $params = array(), $fetchSytle = \PDO::FETCH_OBJ){
        $stm = $this->query($sql, $params);
        return $stm->fetch($fetchSytle);
    }
    
    public function fetchAll($sql, $params = array(), $fetchSytle = \PDO::FETCH_OBJ){
        $stm = $this->query($sql, $params);
        return $stm->fetchAll($fetchSytle);
    }
    
    public function fetchOne($sql, $params = array()){
        $stm = $this->query($sql, $params);
        return $stm->fetchColumn();
    }
	
    public function insert($tableName, $data){
        if(is_object($data)){
            $data = get_object_vars($data);
        }

        $placeHolders = trim(str_repeat('?,', count($data)), ',');
        $sql = 'INSERT INTO ' . $tableName . ' (' . implode(',', array_keys($data)) . ') VALUES (' . $placeHolders . ')';

        $stm = $this->query($sql, array_values($data));

        return $stm->rowCount();
    }
	
    public function update($tableName, $data, array $conditions){
        if(is_object($data)){
            $data = get_object_vars($data);
        }

        $params = array();
        $sql = 'UPDATE ' . $tableName . ' SET ';

        foreach($data as $field=>$value){
            $sql.= $field.'=:'.$field.',';
            $params[':'.$field] = $value;
        }

        $sql = rtrim($sql, ',') . ' WHERE ';

        $idx=0;
        foreach($conditions as $field=>$value){
            if($idx > 0){
                $sql.= ' AND ';
            }

            $sql.= $field.'=:c_'.$field;
            $params[':c_'.$field] = $value;
            ++$idx;
        }

        $stm = $this->query($sql, $params);

        return $stm->rowCount();
    }
    
    public function delete($tableName, array $conditions){

        $params = array();
        $sql = 'DELETE FROM ' . $tableName . ' WHERE ';

        $idx=0;
        foreach($conditions as $field=>$value){
            if($idx > 0){
                $sql.= ' AND ';
            }

            $sql.= $field.'=:c_'.$field;
            $params[':c_'.$field] = $value;
            ++$idx;
        }

        $stm = $this->query($sql, $params);

        return $stm->rowCount();
    }
	
    public function getInsertId($field = null){
        return $this->pdo->lastInsertId($field);
    }
    
    public function getAttribute($attribute){
        return $this->pdo->getAttribute($attribute);
    }
    
    public function getTransaction(){
        return $this->transaction;
    }
    
    public static function getAvailableDrivers(){
        return \PDO::getAvailableDrivers();
    }
}

?>