<?php

namespace System\Data\Entity;

class SqlQuery {
    
    protected $conn;
    protected $sql;
    protected $params;
    
    /**
     * Sets the connection, query and parameters
     * 
     * @method  __construct
     * @param   System.Data.Connection $conn
     * @param   string $sql
     * @param   array $params
     */   
    public function __construct(\System\Data\Database $conn, $sql = null, $params = null){
        $this->conn = $conn;
        $this->sql = $sql;
        $this->params = $params;
    }
    
     /**
     * Sets the query and parameters
     * 
     * @method  setQuery
     * @param   string $sql
     * @param   array $param
     */
    public function setQuery($sql, $param = array()){
        $this->sql = $sql;
        $this->params = $param;
        return $this;
    }
    
     /**
     * Returns the first column of the query
     * 
     * @method  column
     */
    public function column(){
        $stm = $this->conn->query($this->sql, $this->params);
        return $stm->fetchColumn();
    }
    
     /**
     * Returns a single entity by specified name
     * 
     * @method  single
     * @param   string $entityName = null
     * @param   string $default = null
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
     * Returns the entity with the specified view
     * 
     * @method  toList
     * @param   string $entityName = null
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
     * Returns number of rows
     * 
     * @method  nonQuery
     */  
    public function nonQuery(){
        $stm = $this->conn->query($this->sql, $this->params);
        return $stm->rowCount();
    }
    
    /**
     * Returns entity
     * Throws EntityException if a property name cannot be mapped to the db.
     * 
     * @method  toEntity
     * @param   string $data
     * @param   string $entityName
     * @param   string $default = false
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
