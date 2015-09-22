<?php

namespace System\Data\Entity;

class SqlQuery {
    
    protected $conn;
    protected $metaReader;
    protected $sql;
    protected $params;
    
    /**
     * Initializes an instance of SqlQuery with a database connection, entity 
     * meta data, query and parameters.
     * 
     * @method  __construct
     * @param   System.Data.Database $conn
     * @param   System.Data.Entity.MetaReaders.MetaReader $metaReader
     * @param   string $sql = null
     * @param   array $params = null
     */   
    public function __construct(\System\Data\Database $conn, \System\Data\Entity\MetaReaders\MetaReader $metaReader, $sql = null, $params = null){
        $this->conn = $conn;
        $this->metaReader = $metaReader;
        $this->sql = $sql;
        $this->params = $params;
    }
    
     /**
     * Sets the query and parameters
     * 
     * @method  setQuery
     * @param   string $sql
     * @param   array $param = array
     * @return  System.Data.Entity.SqlQuery
     */
    public function setQuery($sql, $param = array()){
        $this->sql = $sql;
        $this->params = $param;
        return $this;
    }
    
     /**
     * Gets the value from the first column of the rowset. If $columnName
     * is specified, then gets the value from the named column.
     * 
     * @method  column
     * @param   string $columnName
     * @return  mixed
     */
    public function column($columnName = ''){
        $stm = $this->conn->query($this->sql, $this->params);
        $row = $stm->fetch(\PDO::FETCH_ASSOC);
        if($columnName){
            return $row[$columnName];
        }
        return reset($row);
    }
    
     /**
     * Gets a single row as an object. If $entityName is specified, then an 
     * instance of $entityName is created and returned where all column names 
     * are mapped to the entity's properties.
     *
     * @method  single
     * @param   string $entityName = null
     * @param   bool $default = false
     * @return  mixed
     */
    public function single($entityName = null, $default = false){
        $stm = $this->conn->query($this->sql, $this->params);

        if($entityName){
            $row = $stm->fetch(\PDO::FETCH_OBJ);
            return $this->toEntity($row, $entityName, $default);
        }
        
        return $stm->fetch(\PDO::FETCH_OBJ);
    }
    
    /**
     * Gets a collection of rows as a DbListResult where each row is respresented 
     * as an object. If $entityName is specified, then an instance of $entityName 
     * is created for each row where all column names are mapped to the entity's properties.
     * 
     * @method  toList
     * @param   string $entityName = null
     * @return  System.Data.Entity.DbListResult
     */
    public function toList($entityName = null){
        $stm = $this->conn->query($this->sql, $this->params);
        
        if($entityName){
            $array = array();
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
            foreach($rows as $row){
                $array[] = $this->toEntity($row, $entityName);
            }
            return new DbListResult($array);
        }else{
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
        }
        return new DbListResult($rows);
    }
    
     /**
     * Executes a non query statement and returns the number of affected rows.
     * 
     * @method  nonQuery
     * @return  int
     */  
    public function nonQuery(){
        $stm = $this->conn->query($this->sql, $this->params);
        return $stm->rowCount();
    }
    
     /**
     * Get the MetaReader object for this instance.
     * 
     * @method  getMetaReader
     * @return  System.Data.Entity.MetaReaders.MetaReader
     */
    public function getMetaReader(){
        return $this->metaReader;
    }
    
    /**
     * Returns an entity
     * Throws EntityException if a property name cannot be mapped to the db.
     * 
     * @method  toEntity
     * @param   mixed $data
     * @param   string $entityName
     * @param   bool $default = false
     * @return  mixed
     */ 
    private function toEntity($data, $entityName, $default = false){

        if(is_callable($entityName)){
            return $entityName($data);
        }
        
        if(is_string($entityName)){
            $class = '\\'.str_replace('.', '\\', $entityName);
            $refClass = new \ReflectionClass($class);
            $entity = $refClass->newInstance();

            if(!$data){
                if($default){ 
                    return $entity;
                }
                return false;
            }
            
            $data = get_object_vars($data);

            if(is_array($data)){
                $properties = $refClass->getProperties();

                foreach($properties as $property){
                    $property->setAccessible(true);
                    $propertyName = $property->getName();

                    if(substr($propertyName, 0,1) != '_'){
                        if(array_key_exists($propertyName, $data)){ 
                            $value = $data[$propertyName];
                            $property->setValue($entity, $value);
                        }else{
                            throw new \System\Data\Entity\EntityException(sprintf("The entity property '%s.%s' could not be mapped to the database result.", $entityName, $propertyName));
                        }
                    }
                }
            }
            return $entity;
        }
    }
}