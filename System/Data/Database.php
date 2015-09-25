<?php

namespace System\Data;

class Database {
    
    protected $dsn = '';
    protected $pdo = null;
    protected $profiler;
    
    /**
     * Constructs a new System.Data.Database instance. Creates a new connection 
     * to a database if connection string is supplied.
     * Throws DatabaseException if the connection to the database failed.
     * 
     * @method  __construct
     * @param   string $connectionString
     * @param   string $uid = ''
     * @param   string $pwd = ''
     * @return  void
     */
    public function __construct($connectionString = null, $uid = '', $pwd = ''){
        $this->profiler = new Profiler();
        if($connectionString){
            $this->connect($connectionString, $uid, $pwd);
        }
    }
    
    /**
     * Creates a new connection to a database using the specified connection string.
     * Throws ConnectionException.
     * 
     * @method  connect
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
     * @method  query
     * @param   string $sql
     * @param   array $params = array()
     * @return  PDOStatement
     */
    public function query($sql, array $params = array()){
        if (!$this->pdo) { return false; }
        
        $this->profiler->start();
        
        try{
            $stm = $this->pdo->prepare($sql);
            $stm->execute($params);
        }catch (\PDOException $e){
            throw new QueryException($e->getMessage());
        }

        $this->profiler->log($sql, $params);

        return $stm;
    }
	
    /**
     * Executes an SQL query and returns a single row.
     * Throws QueryException if an SQL exception occured.
     * 
     * @method  fetch
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
     * @method  fetchAll
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
     * @method  fetchOne
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
     * @method  insert
     * @param   string $tableName
     * @param   mixed $data
     * @return  int
     */
    public function insert($tableName, $data){
        if(is_object($data)){
            $data = \System\Std\Object::getProperties($data);
        }

        $placeHolders = trim(str_repeat('?,', count($data)), ',');
        $sql = 'INSERT INTO ' . $tableName . ' (' . implode(',', array_keys($data)) . ') VALUES (' . $placeHolders . ')';

        $stm = $this->query($sql, array_values($data));

        return $stm->rowCount();
    }
	
    /**
     * Updates table rows and returns the number of rows affected.
     * Throws QueryException if an SQL exception occursed.
     * 
     * @method  update
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
     * @method  delete
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
    
    /**
     * Initiates a transaction.
     * 
     * @method  beginTransaction
     * @return  bool
     */
    public function beginTransaction(){
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commits a transaction.
     * 
     * @method  commit
     * @return  bool
     */
    public function commit(){
        return $this->pdo->commit();
    }
    
    /**
     * Rolls back a transaction.
     * 
     * @method  rollBack
     * @return  bool
     */
    public function rollBack(){
        return $this->pdo->rollBack();
    }
    
    /**
     * Checks if inside a transaction.
     * 
     * @method  inTransaction
     * @return  bool
     */
    public function inTransaction(){
        return $this->pdo->inTransaction();
    }
	
    /**
     * Gets the ID of the last inserted row or sequence value.
     * 
     * @method  getInsertId
     * @param   string $field
     * @return  mixed
     */
    public function getInsertId($field = null){
        return $this->pdo->lastInsertId($field);
    }
    
    /**
     * Gets a database connection attribute.
     * 
     * @method  getAttribute
     * @param   int $attribute
     * @return  mixed
     */
    public function getAttribute($attribute){
        return $this->pdo->getAttribute($attribute);
    }

    /**
     * Gets the connection string used to connect to the database.
     * 
     * @method  getConnectionString
     * @return  string
     */
    public function getConnectionString(){
        return $this->dsn;
    }
    
    /**
     * Gets the profiler object associated with the connection.
     * 
     * @method  getProfiler
     * @return  System.Data.Profiler
     */
    public function getProfiler(){
        return $this->profiler;
    }
    
    /**
     * Gets an array of available PDO drivers.
     * 
     * @method  getAvailableDrivers
     * @return  array
     */
    public static function getAvailableDrivers(){
        return \PDO::getAvailableDrivers();
    }
}