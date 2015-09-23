<?php

namespace System\Data\Entity;

class SqlQuery {
    
    protected $db;
    protected $sql;
    protected $params;
    
    /**
     * Initializes an instance of SqlQuery with a database connection, an entity 
     * meta data collection, query and parameters.
     * 
     * @method  __construct
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
        $stm = $this->db->query($this->sql, $this->params);
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
        $stm = $this->db->query($this->sql, $this->params);

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
        $stm = $this->db->query($this->sql, $this->params);
        
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
        $stm = $this->db->query($this->sql, $this->params);
        return $stm->rowCount();
    }

    /**
     * Get the EntityMetaCollection object for this instance.
     * 
     * @method  getMetaCollection
     * @return  System.Data.Entity.EntityMetaCollection
     */
    public function getMetaCollection(){
        return $this->metaCollection;
    }
    
    /**
     * Returns an entity
     * Throws EntityException if a property name cannot be mapped to the db.
     * 
     * @method  toEntity
     * @param   mixed $data
     * @param   string $entityType
     * @param   bool $default = false
     * @return  mixed
     */ 
    private function toEntity($data, $entityType, $default = false){

        $relationships = array();
        
        if(is_callable($entityType)){
            return $entityType($data);
        }
        
        if(is_array($entityType)){
            foreach($entityType as $entity => $relationship){
                if($entity){
                    if($relationship instanceof Relations\Relationship){
                        $segments = explode(':', (string)$entity, 2);

                        if(count($segments) == 2){
                            list($entityName, $propertyName) = $segments;
                            $relationship->setDbContext($this->dbContext);
                            $relationships[$propertyName] = $relationship;
                        }else{
                            $entityName = $entity;
                        }
                    }else{
                        throw new \InvalidArgumentException(sprintf('Invalid argument specified for "%s". Supplied argument must be an instance of System\Data\Entity\Relations\Relationship', $entity));
                    }
                }else{
                    throw new \InvalidArgumentException(sprintf('Invalid argument specified for relationship mapping. The array must contain keys that represent an entity class and the mapping property name.', $entity));
                }
            }
            $entityType = $entityName;
        }
        
        if(is_string($entityType)){
            $class = '\\'.str_replace('.', '\\', $entityType);
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
                            throw new \System\Data\Entity\EntityException(sprintf("The entity property '%s.%s' could not be mapped to the database result.", $entityType, $propertyName));
                        }
                    }
                }
                
                if(count($relationships) > 0){
                    foreach($relationships as $propertyName => $relationship){
                        $relationship->setParentEntity($entity);
                        \System\Std\Object::setPropertyValue($entity, $propertyName, $relationship->bind());
                    }
                }
            }
            return $entity;
        }
    }
}