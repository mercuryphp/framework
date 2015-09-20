<?php

namespace System\Data\Entity;

use System\Std\Object;
use System\Data\Database;

abstract class DbContext {
    
    protected $conn = null;
    protected $dbSets;
    protected $persistedEntities;
    
    /**
     * Initializes an instance of DbContext.
     * 
     * @method  __construct
     * @param   string $connectionString = null
     */
    public function __construct($connectionString = null){
        
        if($connectionString == null){
            $segments = explode('\\', get_called_class());
            $dbName = array_pop($segments);
            
            $connectionString = sprintf("driver=mysql;host=127.0.0.1;dbname=%s;charset=utf8;uid=root;pwd=", $dbName);
        }
        
        $this->db = new Database($connectionString);
        $this->dbSets = new \System\Collections\Dictionary();
        $this->persistedEntities = new \System\Collections\Dictionary();
    }
    
    /**
     * Gets the underlying database instance.
     * 
     * @method  getDatabase
     * @return  System.Data.Database
     */
    public function getDatabase(){
        return $this->db;
    }
    
    /**
     * Issues an SQL query and returns a new SqlQuery instance.
     * 
     * @method  query
     * @param   string $sql
     * @param   array $params
     * @return  System.Data.Entity.SqlQuery
     */
    public function query($sql, $params = array()){
        return new SqlQuery($this->db, $sql, $params);
    }
    
    /**
     * Registers an entity type in the DbSet collection.
     * 
     * @method  dbSet
     * @param   string $entityName
     * @return  System.Data.Entity.DbSet
     */
    public function dbSet($entityName){
        if(!$this->dbSets->hasKey($entityName)){
            $metaData = MetaReader::getMeta($entityName);
            $this->dbSets[$entityName] = new DbSet($this, $metaData);
        }
        return $this->dbSets[$entityName];
    }
    
    /**
     * Gets the DbSet collection.
     * 
     * @method  getDbSets
     * @return  System.Collections.Dictionary
     */
    public function getDbSets(){
        return $this->dbSets;
    }
    
    /**
     * Gets a collection of persisted entities.
     * 
     * @method  getPersistedEntities
     * @return  System.Collections.Dictionary
     */
    public function getPersistedEntities(){
        return $this->persistedEntities;
    }

    /**
     * Applys all changes made in this context to the underlying database.
     * 
     * @method  saveChanges
     * @return  int
     */
    public function saveChanges(){
        $log = array();
        
        foreach($this->dbSets as $set){
            $meta = $set->getMeta();
            $entities = $set->getEntities();
            
            foreach($entities as $entityContext){
                $entity = $entityContext->getEntity();
                
                $properties = Object::getProperties($entity);

                foreach($properties as $property=>$value){
                    if(is_object($value)){
                        $parentEntityHash = spl_object_hash($value);
                        
                        if($this->persistedEntities->hasKey($parentEntityHash)){
                            $parentEntityContext = $this->persistedEntities->get($parentEntityHash);
                            $parentMeta = $this->dbSets[$parentEntityContext->getName()]->getMeta();
                            $properties[$property] = Object::getPropertyValue($value, $parentMeta->getKey()->getKeyName());
                        }
                    }
                    
                    if($value instanceof \System\Std\Date){
                        $properties[$property] = $value->toString('yyyy-MM-dd HH:mm:ss');
                    }
                    
                    $columnAttributes = $meta->getColumnAttributes($property);

                    foreach($columnAttributes as $attribute){
                        
                        if($attribute instanceof \System\Data\Entity\Attributes\ConstraintAttribute){
                            $attribute->setColumnName($property);
                            $attribute->setValue($value);
                            if(!$attribute->isValid()){
                                throw new Attributes\ConstraintAttributeException($attribute->getErrorMessage());
                            }
                        }
                        
                        if($attribute instanceof \System\Data\Entity\Attributes\DefaultValue){
                            if(is_null($value)){
                                $properties[$property] = $attribute->getDefaultValue();
                                Object::setPropertyValue($entity, $property, $attribute->getDefaultValue());
                            }
                        }
                    }
                }

                switch ($entityContext->getState()){
                    case EntityContext::PERSIST:

                        $result = $this->db->insert($meta->getTable()->getTableName(), $properties);
                        $log[] = $result;
                        
                        if($result){
                            Object::setPropertyValue($entity, $meta->getKey()->getKeyName(), $this->db->getInsertId($meta->getKey()->getKeyName()));
                            $entityContext->setState(EntityContext::PERSISTED);
                            $this->persistedEntities->add($entityContext->getHashCode(), $entityContext);
                        }
                        break;

                    case EntityContext::PERSISTED:
                        $updateParams = array($meta->getKey()->getKeyName() => $properties[$meta->getKey()->getKeyName()]);
                        $log[] = $this->db->update($meta->getTable()->getTableName(), $properties, $updateParams);
                        break;
                    
                    case EntityContext::DELETE:
                        $updateParams = array($meta->getKey()->getKeyName() => $properties[$meta->getKey()->getKeyName()]);
                        $log[] = $this->db->delete($meta->getTable()->getTableName(), $properties, $updateParams);
                        $this->persistedEntities->removeAt($entityContext->getHashCode());
                        $set->detach($entity);
                        break;
                }
            }
        }
        
        return array_sum($log);
    }
}