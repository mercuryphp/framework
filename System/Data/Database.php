<?php

namespace System\Data;

class Database {
    
    protected $dsn;
    protected $pdo;
    protected $queryParams;
    protected $profiler;
    
    /**
     * Constructs a new System.Data.Database instance. Creates a new connection 
     * to a database if connection string is supplied.
     * Throws DatabaseException if the connection to the database failed.
     * 
     * @param   string $connectionString
     * @param   string $uid = ''
     * @param   string $pwd = ''
     * @return  void
     */
    public function __construct($connectionString = null, $uid = '', $pwd = ''){
        $this->profiler = new Profiler();
        $this->queryParams = new \System\Collections\Dictionary();
        
        if($connectionString){
            $this->connect($connectionString, $uid, $pwd);
        }
    }
    
    /**
     * Creates a new connection to a database using the specified connection string.
     * Throws ConnectionException.
     * 
     * @param   string $connectionString
     * @param   string $uid = ''
     * @param   string $pwd = ''
     * @return  void
     */
    public function connect($connectionString, $uid = '', $pwd = ''){
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
        
        $uid = isset($dbConfig['uid']) ? $dbConfig['uid'] : $uid;
        $pwd = isset($dbConfig['pwd']) ? $dbConfig['pwd'] : $pwd;
        
        unset($dbConfig['uid']);
        unset($dbConfig['pwd']);

        $this->dsn = $dbConfig->each(function($v, $k){ return $k.'='.$v.';'; })->join('');
        
        try{
            $this->profiler->start();
            $this->pdo = new \PDO($this->dsn, $uid, $pwd);
            $this->profiler->log('Connected to database', $dbConfig->toArray(), Profiler::CONNECT);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }catch(\PDOException $e){
            throw new DatabaseException($e->getMessage());
        }
    }
    
    /**
     * Executes an SQL query and returns a PDOStatement object.
     * Throws QueryException if an SQL exception occured.
     * 
     * @param   string $sql
     * @param   array $params = array()
     * @return  PDOStatement
     */
    public function query($sql, array $params = array()){
        if (!$this->pdo) { return false; }
        
        $sqlString = \System\Std\Str::set($sql);
        $this->profiler->start();

        if($this->queryParams->hasItems()){ 
            foreach($this->queryParams as $k=>$v){ 
                if($sqlString->indexOf('@'.$k) > -1){
                    $sqlString = $sqlString->replace('@'.$k, ':'.$k);
                    $params[$k] = $v;
                }
            }
        }
        
        try{
            $stm = $this->pdo->prepare($sqlString->toString());
            $stm->execute($params);
        }catch (\PDOException $e){
            throw new QueryException($e->getMessage(), $sql, $params, $e->getCode());
        }

        $this->profiler->log($sql, $params);

        return $stm;
    }
	
    /**
     * Executes an SQL query and returns a single row.
     * Throws QueryException if an SQL exception occured.
     * 
     * @param   string $sql
     * @param   array $params = array()
     * @param   int $fetchSytle = PDO::FETCH_OBJ
     * @return  mixed
     */
    public function fetch($sql, $params = array(), $fetchSytle = \PDO::FETCH_OBJ){
        $stm = $this->query($sql, $params);
        return $stm->fetch($fetchSytle);
    }
    
    /**
     * Executes an SQL query and returns a collection of rows.
     * Throws QueryException if an SQL exception occured.
     * 
     * @param   string $sql
     * @param   array $params = array()
     * @param   int $fetchSytle = PDO::FETCH_OBJ
     * @return  array
     */
    public function fetchAll($sql, $params = array(), $fetchSytle = \PDO::FETCH_OBJ){
        $stm = $this->query($sql, $params);
        return $stm->fetchAll($fetchSytle);
    }
    
    /**
     * Executes an SQL query and returns the first column from the first row.
     * Throws QueryException if an SQL exception occured.
     * 
     * @param   string $sql
     * @param   array $params = array()
     * @return  mixed
     */
    public function fetchOne($sql, $params = array()){
        $stm = $this->query($sql, $params);
        return $stm->fetchColumn();
    }
	
    /**
     * Inserts data into a table and returns the number of rows affected.
     * Throws QueryException if an SQL exception occured.
     * 
     * @param   string $tableName
     * @param   mixed $data
     * @return  int
     */
    public function insert($tableName, $data){
        if(is_object($data)){
            $data = \System\Std\Object::getProperties($data);
        }

        $params = array();
        $sql = 'INSERT INTO ' . $tableName . '(' . join(',', array_keys($data)) . ') VALUES (';
        
        foreach($data as $field=>$value){
            if($value instanceof DbFunction){
                $sql.= $value->toString().',';
            }else{
                $sql.= ':'.$field.',';
                $params[':'.$field] = $value;
            }
        }

        $sql = trim($sql, ',').')';
        $stm = $this->query($sql, $params);

        return $stm->rowCount();
    }
	
    /**
     * Updates table rows and returns the number of rows affected.
     * Throws QueryException if an SQL exception occursed.
     * 
     * @param   string $tableName
     * @param   mixed $data
     * @param   array $conditions
     * @return  int
     */
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
    
    /**
     * Deletes table rows and returns the number of rows affected.
     * Throws QueryException if an SQL exception occured.
     * 
     * @param   string $tableName
     * @param   array $conditions
     * @return  int
     */
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
    
    public function queryParams(){
        return $this->queryParams;
    }

    /**
     * Initiates a transaction.
     * 
     * @return  bool
     */
    public function beginTransaction(){
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commits a transaction.
     * 
     * @return  bool
     */
    public function commit(){
        return $this->pdo->commit();
    }
    
    /**
     * Rolls back a transaction.
     * 
     * @return  bool
     */
    public function rollBack(){
        return $this->pdo->rollBack();
    }
    
    /**
     * Checks if inside a transaction.
     * 
     * @return  bool
     */
    public function inTransaction(){
        return $this->pdo->inTransaction();
    }
	
    /**
     * Gets the ID of the last inserted row or sequence value.
     * 
     * @param   string $field
     * @return  mixed
     */
    public function getInsertId($field = null){
        return $this->pdo->lastInsertId($field);
    }
    
    /**
     * Gets a database connection attribute.
     * 
     * @param   int $attribute
     * @return  mixed
     */
    public function getAttribute($attribute){
        return $this->pdo->getAttribute($attribute);
    }

    /**
     * Gets the connection string used to connect to the database.
     * 
     * @return  string
     */
    public function getConnectionString(){
        return $this->dsn;
    }
    
    /**
     * Gets the profiler object associated with the connection.
     * 
     * @return  System.Data.Profiler
     */
    public function getProfiler(){
        return $this->profiler;
    }
    
    /**
     * Gets an array of available PDO drivers.
     * 
     * @return  array
     */
    public static function getAvailableDrivers(){
        return \PDO::getAvailableDrivers();
    }
}