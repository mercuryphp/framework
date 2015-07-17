<?php

namespace System\Data\Entity;

use System\Data\Connection;

abstract class DbContext {
    
    protected $conn = null;
    protected $dbSets = array();
    protected $persistedEntities = null;
    
    public function __construct($connectionString = null){
        
        if($connectionString == null){
            $segments = explode('\\', get_called_class());
            $dbName = array_pop($segments);
            
            $connectionString = sprintf("driver=mysql;host=127.0.0.1;dbname=%s;charset=utf8;uid=root;pwd=", $dbName);
        }
        
        $this->conn = new Connection($connectionString);
        $this->persistedEntities = new \System\Collections\Dictionary();
    }
    
    public function getConnection(){
        return $this->conn;
    }
    
    public function query($sql, $params = array()){
        return new SqlQuery($this->conn, $sql, $params);
    }
    
    public function select($fields, $entityName){
        return new SelectQuery(new SqlQuery($this->conn), $fields, $entityName);
    }
    
    public function table($tableName, $params = array()){
        return new SqlQuery($this->conn, 'SELECT * FROM '.$tableName, $params);
    }
    
    public function dbSet($entityName){
        if(!array_key_exists($entityName, $this->dbSets)){
            $metaData = MetaReader::getMeta($entityName);
            $this->dbSets[$entityName] = new DbSet($this, $metaData);
        }
        
        return $this->dbSets[$entityName];
    }
    
    public function getPersistedEntities(){
        return $this->persistedEntities;
    }
    
    public function getDbSets(){
        return $this->dbSets;
    }

    public function saveChanges(){
        $log = array();
        
        foreach($this->dbSets as $set){
            $meta = $set->getMeta();
            $entities = $set->getEntities();
            
            foreach($entities as $entityContext){
                $entity = $entityContext->getEntity();
                
                $properties = $this->getProperties($entity);

                foreach($properties as $property=>$value){
                    if(is_object($value)){
                        $parentEntityHash = spl_object_hash($value);
                        
                        if($this->persistedEntities->hasKey($parentEntityHash)){
                            $parentEntityContext = $this->persistedEntities->get($parentEntityHash);
                            $parentMeta = $this->dbSets[$parentEntityContext->getName()]->getMeta();
                            $properties[$property] = $this->getEntityPropertyValue($value, $parentMeta->getKey()->getKeyName());
                        }
                    }
                    
                    if($value instanceof \System\Std\Date){
                        $properties[$property] = $value->toString('yyyy-MM-dd HH:mm:ss');
                    }
                    
                    $columnAttributes = $meta->getColumnAttributes($property);

                    foreach($columnAttributes as $attribute){
                        
                        if($attribute instanceof \System\Data\Entity\Annotations\ValidationAttribute){
                            $attribute->setColumnName($property);
                            $attribute->setValue($value);
                            if(!$attribute->isValid()){
                                throw new Annotations\ValidationAttributeException($attribute->getErrorMessage());
                            }
                        }
                        
                        if($attribute instanceof \System\Data\Entity\Annotations\DefaultValue){
                            if(is_null($value)){
                                $properties[$property] = $attribute->getDefaultValue();
                                $this->setEntityPropertyValue($entity, $property, $attribute->getDefaultValue());
                            }
                        }
                    }
                }

                switch ($entityContext->getState()){
                    case EntityContext::PERSIST:

                        $result = $this->conn->insert($meta->getTable()->getTableName(), $properties);
                        $log[] = $result;
                        
                        if($result){
                            $this->setEntityPropertyValue($entity, $meta->getKey()->getKeyName(), $this->conn->getInsertId($meta->getKey()->getKeyName()));
                            $entityContext->setState(EntityContext::PERSISTED);
                            $this->persistedEntities->add($entityContext->getHashCode(), $entityContext);
                        }
                        break;

                    case EntityContext::PERSISTED:
                        $updateParams = array($meta->getKey()->getKeyName() => $properties[$meta->getKey()->getKeyName()]);
                        $log[] = $this->conn->update($meta->getTable()->getTableName(), $properties, $updateParams);
                        break;
                }
            }
        }
        
        return array_sum($log);
    }
    
    private function getProperties($entity){
        $refClass = new \ReflectionClass($entity);
        $properties = $refClass->getProperties();
        
        $array = array();
        foreach($properties as $property){
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($entity);
            
            $array[$name] = $value;
        }
        return $array;
    }
    
    private function setEntityPropertyValue($entity, $propertyName, $value){
        $refClass = new \ReflectionClass($entity);

        if($refClass->hasProperty($propertyName)){
            $property = $refClass->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($entity , $value);
        }
    }
    
    private function getEntityPropertyValue($entity, $propertyName){
        $refClass = new \ReflectionClass($entity);

        if($refClass->hasProperty($propertyName)){
            $property = $refClass->getProperty($propertyName);
            $property->setAccessible(true);
            return $property->getValue($entity);
        }
    }
}

?>