<?php

namespace System\Data\Entity;

class SqlQuery {
    
    protected $db;
    protected $metaCollection;
    protected $sql;
    protected $params;
    
    /**
     * Initializes an instance of SqlQuery with a database connection, an entity 
     * meta data collection, query and parameters.
     * 
     * @param   System.Data.Database $db
     * @param   System.Data.Entity.EntityMetaCollection $metaCollection
     * @param   string $sql = null
     * @param   array $params = null
     */   
    public function __construct(\System\Data\Database $db, \System\Data\Entity\EntityMetaCollection $metaCollection, $sql = null, array $params = array()){
        $this->db = $db;
        $this->metaCollection = $metaCollection;
        $this->sql = $sql;
        $this->params = $params;
    }
    
     /**
     * Sets the query and parameters
     * 
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
     * @param   string $columnName
     * @return  mixed
     */
    public function column($columnName = ''){
        $stm = $this->db->query($this->sql, $this->params);
        $row = $stm->fetch(\PDO::FETCH_ASSOC);
        if($columnName){
            return $row[$columnName];
        }
        if(is_array($row)){
            return reset($row);
        }
    }
    
     /**
     * Gets a single row as an object. If $entityType is specified as a string,
     * then an instance of $entityType is created and returned where all column 
     * names are mapped to the entity's properties. If $default is specified and 
     * $entityType is a string, then gets a default object if no record is found.
     *
     * @param   mixed $entityType = null
     * @param   bool $default = false
     * @return  mixed
     */
    public function single($entityType = null, $default = false){
        $stm = $this->db->query($this->sql, $this->params);

        if($entityType){
            $row = $stm->fetch(\PDO::FETCH_OBJ);
            return $this->toEntity($row, $entityType, $default);
        }
        
        return $stm->fetch(\PDO::FETCH_OBJ);
    }
    
    /**
     * Gets a collection of rows as a DbListResult where each row is respresented 
     * as an object. If $entityType is specified as a string, then an instance of 
     * $entityType is created for each row where all column names are mapped to 
     * the entity's properties. $entityType can also be a callback function that 
     * is passed a row set or an instance of System.Data.Entity.Relations.Relationship.
     * 
     * @param   mixed $entityType = null
     * @return  System.Data.Entity.DbListResult
     */
    public function toList($entityType = null){
        $stm = $this->db->query($this->sql, $this->params);
        
        if($entityType){
            $array = array();
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
            foreach($rows as $row){
                $array[] = $this->toEntity($row, $entityType);
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
     * @return  int
     */  
    public function nonQuery(){
        $stm = $this->db->query($this->sql, $this->params);
        return $stm->rowCount();
    }

    /**
     * Gets the EntityMetaCollection object.
     * 
     * @return  System.Data.Entity.EntityMetaCollection
     */
    public function getMetaCollection(){
        return $this->metaCollection;
    }

    /**
     * The toEntity method returns an entity of a specified type. If no type is 
     * specified, then the row data is returned as an stdClass object. The type can be
     * specified as a string e.g Models.User where Models is the namespace 
     * and User is the entity class. The type can also be specified as a callback
     * function. The function is supplied with row data. You are required to create 
     * your own model instance then return it.
     * 
     *  function($data){
     *      $user = new \Models\User();
     *      $user->setFirstName($data->first_name);
     *      $user->setlastName($data->last_name);
     *      return $user;
     *  }
     *
     * The callback function provides more flexibility allowing you to manipulate
     * the data before it is returned. An instance of 
     * System.Data.Entity.Relations.Relationship maybe passed as the type, which
     * allows you to construct relationships. For more information on relationships
     * consult the API library.
     */
    private function toEntity($data, $entityType, $default = false){

        $relationships = array();

        if(is_callable($entityType) && $data){
            return $entityType($data);
        }

        if($entityType instanceof Relations\Relationship){
            $entityType->setDatabase($this->db);
            $entityType->setMetaCollection($this->metaCollection);
            $relationships = $entityType->getChildRelationships();
            $entityType = $entityType->getPrincipalEntityName();
        }

        if(is_string($entityType)){
            $class = '\\'.str_replace('.', '\\', $entityType);
            
            try{
                $refClass = new \ReflectionClass($class);
            }catch(\Exception $e){
                throw new EntityNotFoundException($class, $data);
            }
            
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
                            throw new \System\Data\Entity\EntityException(sprintf("The entity property '%s.%s' could not be mapped to the database result.", $entityType, $propertyName));
                        }
                    }
                }

                if(count($relationships) > 0){
                    foreach($relationships as $propertyName => $relationship){
                        $relationship->setDatabase($this->db);
                        $relationship->setMetaCollection($this->metaCollection);
                        $relationship->setDependantEntity($entity);
                        \System\Std\Object::setPropertyValue($entity, $propertyName, $relationship->execute());
                    }
                }
            }
            return $entity;
        }
    }
}