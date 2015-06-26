<?php

namespace System\Data;

class Connection {
    
    protected $dbConfig;
    
    public function __construct($connectionString){
        $this->dbConfig = new \System\Collections\Dictionary();
        
        if(strpos($connectionString, ';')){
            $params = explode(';', $connectionString);
            foreach($params as $param){
                if($param && strpos($param, '=')){
                    list($name, $value) = explode('=', $param, 2);
                    $this->dbConfig[$name] = $value;
                }
            }
        }
        
        $uid = $this->dbConfig['uid'];
        $pwd = $this->dbConfig['pwd'];
        
        unset($this->dbConfig['uid']);
        unset($this->dbConfig['pwd']);

        try{
            $this->pdo = new \PDO(
                $this->dbConfig->each(function($k, $v){ return $k.'='.$v.';'; })->join(''),
                $uid,
                $pwd
            );

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

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
    
    public static function getAvailableDrivers(){
        return \PDO::getAvailableDrivers();
    }
}

?>